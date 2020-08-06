<?php

namespace Tests\Feature;

use App\Product;
use RetailersSeeder;
use Tests\TestCase;

class TrackerAddCommandTest extends TestCase
{
    /** @test */
    public function it_adds_a_product_to_the_tracker()
    {
        $this->seed(RetailersSeeder::class);

        $this->assertEmpty(Product::all());

        $this->artisan('tracker:add BestBuy Nintendo 6364253');

        $this->assertEquals(1, Product::count());
    }
}
