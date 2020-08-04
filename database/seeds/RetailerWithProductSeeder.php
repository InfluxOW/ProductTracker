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
        $bestBuy = Retailer::create(['name' => 'BestBuy']);
        $switch = $bestBuy->products()->create([
            'name' => 'Nintendo Switch',
            'sku' => 6364253, // Nintendo Switch SKU
            'in_stock' => false,
        ]);

        factory(User::class)->create(['name' => 'admin', 'email' => 'admin@admin.com']);
    }
}
