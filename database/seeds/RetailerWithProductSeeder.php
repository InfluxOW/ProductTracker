<?php

use Illuminate\Database\Seeder;
use App\Product;
use App\Retailer;
use App\Stock;
use App\User;

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
            'sku' => 6364253,
        ]));

        factory(User::class)->create();
    }
}
