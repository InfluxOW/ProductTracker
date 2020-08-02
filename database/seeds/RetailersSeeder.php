<?php

use Illuminate\Database\Seeder;
use App\Retailer;

class RetailersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Retailer::class)->state('BestBuy')->create();
    }
}
