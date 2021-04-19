<?php

use Illuminate\Support\Facades\Route;
use App\Product;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    // $options = array(
    //     CURLOPT_RETURNTRANSFER => true,
    //     CURLOPT_SSL_VERIFYPEER => false,
    //     CURLOPT_URL => 'https://aliexpress.ru/item/'.$request->product_uid.'.html'
    // );

    // $curl = curl_init();
    // curl_setopt_array($curl, $options);
    // $html = curl_exec($curl);
    // curl_close($curl);

    // $pricePreg = preg_match('/(?:totalValue: ")(.*?)(?:")/', $html, $matches);

    // $price = substr($matches[1], 0, strpos($matches[1], " "));

    // dd($price);

    return view('welcome');
});

Auth::routes();

Route::get('{path}', 'DashboardController@index')->where('path', '([A-z\d\-\/_.]+)?');