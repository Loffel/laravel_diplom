<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Price;

class ProductController extends Controller
{

    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct(){
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return auth('api')->user()->products()->latest()->with('shop')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $options = array(
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_SSL_VERIFYPEER => false,
        //     CURLOPT_URL => 'https://aliexpress.ru/item/32958909860.html'
        // );

        // $curl = curl_init();
        // curl_setopt_array($curl, $options);
        // $html = curl_exec($curl);
        // curl_close($curl);

        // $priceStart = strpos($html, 'totalValue');
        // $priceQuote1 = strpos($html, '"', $priceStart);
        // $priceQuote2 = strpos($html, '"', $priceQuote1 + 1);

        // $price = substr($html, $priceQuote1 + 1, $priceQuote2 - $priceQuote1 - 1);

        $shopURL = '';
        if($request->shop_id == 1) $shopURL = 'https://www.pricearchive.org/orpricetable?action=show&item='.$request->product_uid.'&store=aliexpress.com&dateformat=8&priceformat=1&delimitersformat=2&nodatadays=0';
        else if($request->shop_id == 2) $shopURL = 'https://wbmonitor.ru/template/view_product_beru.php?sku='.$request->product_uid;

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $shopURL
        );
    
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $html = curl_exec($curl);
        curl_close($curl);

        $invalidID = false;
        if($request->shop_id == 1){
            if($html == "<tr><td></td><td></td></tr>") $invalidID = true;
        }else if($request->shop_id == 2) {
            if(strpos($html, "нет истории цен") !== FALSE) $invalidID = true;
        }

        if($invalidID) return response()->json(['message' => 'Неверный uid продукта'], 404);

        $this->validate($request, [
            'title' =>  'required|string|max:255',
            'shop_id'   =>  'required|exists:shops,id',
            'product_uid'   =>  'required|string|max:50',
            'comment'   =>  'max:200'
        ]);

        Product::create([
            'title'         =>  $request->title,
            'user_id'       =>  auth('api')->user()->id,
            'shop_id'       =>  $request->shop_id,
            'product_uid'   =>  $request->product_uid,
            'comment'       =>  $request->comment
        ]);

        return [
            'message' => 'Продукт добавлен'
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(['prices', 'shop'])->findOrFail($id);

        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'     => 'required|string|max:255',
            'comment'   => 'sometimes|max:200'
        ]);

        $product = Product::findOrFail($id);

        $product->update([
            'title'     => $request->title,
            'comment'   => $request->comment
        ]);

        return [
            'message' => 'Продукт обновлён!'
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return [
            'message' => 'Продукт удалён!'
        ];
    }

    public function getExchangeRate(){
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => 'https://api.ratesapi.io/api/latest?base=USD&symbols=RUB'
        );
    
        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $html = curl_exec($curl);
        curl_close($curl);

        $html = json_decode($html);

        return response()->json($html, 200);
    }
}
