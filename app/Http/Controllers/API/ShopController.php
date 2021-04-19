<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Shop;

class ShopController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct(){
        $this->middleware('auth:api');
        $this->middleware('admin')->except(['index']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Shop::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' =>  'required|string|max:255',
            'url'   =>  'required|unique:shops,url|url',
            'url_product'   =>  'required|string|max:50',
            'template'   =>  'required|string|regex:/.*\*.*/i'
        ]);

        Shop::create([
            'title'         =>  $request->title,
            'url'       =>  $request->url,
            'url_product'       =>  $request->url_product,
            'template'   =>  $request->template,
        ]);

        return [
            'message' => 'Магазин добавлен'
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
        //
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
        $shop = Shop::findOrFail($id);

        $this->validate($request, [
            'title' =>  'required|string|max:255',
            'url'   =>  'required|unique:shops,url,' . $shop->id . '|url',
            'url_product'   =>  'required|string|max:50',
            'template'   =>  'required|string|regex:/.*\*.*/i'
        ]);
        
        $shop->update([
            'title' =>  $request->title,
            'url'   =>  $request->url,
            'url_product'   =>  $request->url_product,
            'template'   =>  $request->template
        ]);

        return [
            'message' => 'Магазин обновлён!'
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
        $shop = Shop::findOrFail($id);

        $shop->delete();

        return [
            'message' => 'Магазин удалён!'
        ];
    }
}
