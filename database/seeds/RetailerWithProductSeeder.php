<?php

use Illuminate\Database\Seeder;
use App\Product;
use App\Retailer;
use App\Stock;

class RetailerWithProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $switch = Product::create(['name' => 'Nintendo Switch']);
        $bestBuy = Retailer::create(['name' => 'Best Buy']);

        $bestBuy->addStock($switch, new Stock([
            'price' => 10000,
            'url' => 'http://foobar.example',
            'sku' => 12345,
            'in_stock' => false
        ]));
    }
}
