<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Shop;

class ShopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Shop::truncate();
        Schema::enableForeignKeyConstraints();

        Shop::create([
            'title'         =>  'Aliexpress',
            'url'           =>  'http://aliexpress.com/',
            'url_product'   =>  'item/*.html',
            'template'      =>  '/(?:totalValue: ")(.*?)(?:")/'
        ]);

        Shop::create([
            'title'         =>  'Беру',
            'url'           =>  'https://beru.ru/',
            'url_product'   =>  'product/*',
            'template'      =>  'data:',
            'currency'      =>  'RUB'
        ]);
    }
}
