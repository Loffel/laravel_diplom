<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;

class PredictPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'predict:prices';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Price prediction';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $products = Product::all();
        foreach($products as $product){
            $product->predict();
        }
    }
}
