<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Price;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'user_id', 'shop_id', 'product_uid', 'comment'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function shop(){
        return $this->belongsTo('App\Shop');
    }

    public function prices(){
        return $this->hasMany('App\Price');
    }

    /**
     * Функция МНК для прогноза
     *
     * @param integer $daysCount
     * @return void
     */
    public function predict($daysCount = 7){
        $data = [];

        $this->prices()->delete();

        switch($this->shop->id){
            case 1: //aliexpress
                $options = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_URL => 'https://www.pricearchive.org/orpricetable?action=show&item='.$this->product_uid.'&store=aliexpress.com&dateformat=8&priceformat=1&delimitersformat=2&nodatadays=0'
                );
            
                $curl = curl_init();
                curl_setopt_array($curl, $options);
                $html = curl_exec($curl);
                curl_close($curl);
            
                $html = array_filter(explode('</tr>', str_replace('<tr>','',$html)));
                
                foreach($html as $value){
                    if(strpos($value, 'hidden') === FALSE){
                        $newValues = array_filter(explode('</td>', str_replace('<td>', '', $value)));
            
                        if(empty($newValues)) continue;
            
                        $data[$newValues[0]] = floatval($newValues[1]);
                    }
                }

                break;
            case 2: //beru
                $options = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_URL => 'https://wbmonitor.ru/template/view_product_beru.php?sku='.$this->product_uid
                );
            
                $curl = curl_init();
                curl_setopt_array($curl, $options);
                $html = curl_exec($curl);
                curl_close($curl);

                $chartStart = strpos($html, 'Highcharts.chart');
                if($chartStart !== FALSE){
                    $dataStart = strpos($html, 'data:', $chartStart);

                    $dataStart = substr($html, $dataStart + 5);
                    $dataStart = str_replace(["\n","\t", "\r", " "], '', $dataStart);
                    $dataStart = substr($dataStart, 0, strpos($dataStart, ',point:'));

                    
                    $dataStart = str_replace("),", ");", $dataStart);
                    $arrayShit = array_filter(explode(']', str_replace('[','',$dataStart)));
                    foreach($arrayShit as $arData){
                        if(strpos($arData, "Date.UTC") === FALSE) continue;

                        if($arData[0] == ",")
                            $arData = substr($arData, 1);
                        
                        $tempData = explode(';', $arData);
                        $tempData[1] = floatval($tempData[1]);
                        $tempData[0] = str_replace(["Date.UTC(", ")"], "", $tempData[0]);
                        $tempData[0] = str_replace(',', '-', $tempData[0]);
                        $tempData[0] = new \DateTime($tempData[0]);
                        $tempData[0] = $tempData[0]->format("Y-m-d");

                        $data[$tempData[0]] = $tempData[1];
                    }
                }
                break;
        }
        
        ksort($data);
        
        foreach($data as $date => $dateValue){
            Price::create([
                'product_id' => $this->id,
                'value' => $dateValue,
                'type' => 'real',
                'date' => $date
            ]);
        }

        $prices = $this->prices;

        $lastDate = $prices->where('type', 'real')->sortByDesc('date')->first()->date; // remove

        $dataToAnalyze = $prices->where('type', 'real');

        $datesCount = $dataToAnalyze->count();
        
        $analyzeHalf = round($datesCount / 2);

        $sumDates = 0; // S(x)
        $sumValues = 0; // S(y)
        $sumXY = 0; // S(xy)
        $sumX2 = 0; // S(x2)
        $sumY2 = 0; // S(y2)

        for($i = 0; $i < $datesCount; $i++){
            $dateEpoch = intval($dataToAnalyze[$i]->date->format('U'));

            $sumDates += $dateEpoch;
            $sumValues += $dataToAnalyze[$i]->value;
            $sumXY += $dateEpoch * $dataToAnalyze[$i]->value;
            $sumX2 += $dateEpoch * $dateEpoch;
            $sumY2 += $dataToAnalyze[$i]->value * $dataToAnalyze[$i]->value;
        }
        
        //
        $datesCount = $dataToAnalyze->count();

        $datesAverage = $sumDates / $datesCount;
        $valuesAverage = $sumValues / $datesCount;

        $bVar = ($datesCount * $sumXY - $sumDates * $sumValues) / ($datesCount * $sumX2 - $sumDates * $sumDates);
        $aVar = $valuesAverage - $bVar * $datesAverage;

        // $lastDate = $dataToAnalyze[$analyzeHalf - 1]->date;
        $remDays = $datesCount - $analyzeHalf;

        for($i = 1; $i <= $remDays + $daysCount - 1; ++$i){

            if($i < $remDays) $currentDate = $dataToAnalyze[$analyzeHalf + $i]->date;
            else {
                $currentDate = $lastDate->addDays(1);

                // echo ($currentDate->format('Y-m-d')).PHP_EOL;
            }

            
            $predictedDate['product_id'] = $this->id;
            $predictedDate['value'] = round($aVar + $bVar * intval($currentDate->format('U')), 2);
            $predictedDate['date'] = $currentDate->format('Y-m-d');
            $predictedDate['type'] = 'predict';
            Price::create($predictedDate);

            if($i <= $remDays + 7){
                $indexOffset = $i < $remDays ? $analyzeHalf + $i : $datesCount - 1;
                $sumShit = 0;

                for($j = 0; $j < $remDays; $j++){
                    if($analyzeHalf + $i - 1 - $remDays + $j < $analyzeHalf){
                        $sumShit += $dataToAnalyze[$analyzeHalf + $i - 1 - $remDays + $j]->value;
                    }
                }

                

                if($i < $remDays){
                    $prevPredict = $sumShit / $remDays;

                    $prevData[0] = ($analyzeHalf + ($i - 1) - 1 >= $analyzeHalf) ? 0 : $dataToAnalyze[$analyzeHalf + ($i - 1) - 1]->value;
                    $prevData[1] = ($analyzeHalf + ($i - 1) - 2 >= $analyzeHalf) ? 0 : $dataToAnalyze[$analyzeHalf + ($i - 1) - 2]->value;

                    $predictedDate['value'] = ($prevData[0] -$prevData[1]) / $remDays + $prevPredict;
                }else{
                    $prevPredict = ($dataToAnalyze[$indexOffset - 1]->value + $dataToAnalyze[$indexOffset - 2]->value + $dataToAnalyze[$indexOffset - 3]->value + $dataToAnalyze[$indexOffset - 4]->value + $dataToAnalyze[$indexOffset - 5]->value + $dataToAnalyze[$indexOffset - 6]->value + $dataToAnalyze[$indexOffset - 7]->value) / 7;
                    $predictedDate['value'] = ($dataToAnalyze[$indexOffset - 1]->value - $dataToAnalyze[$indexOffset - 2]->value) / 7 + $prevPredict;
                }

                $predictedDate['type'] = 'predict_avr';
                Price::create($predictedDate);
            }
        }
    }
}
