<?php

namespace Tests\Feature;

use App\Product;
use RetailerWithProductSeeder;
use Tests\TestCase;

class TrackerUntrackCommandTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();

        $this->seed(RetailerWithProductSeeder::class);
    }

    /** @test */
    public function it_can_untrack_a_product()
    {
        $product = Product::first();

        $this->assertCount(1, Product::all());

        $this->artisan("tracker:untrack '{$product->name}'")
            ->assertExitCode(0);

        $this->assertEmpty(Product::all());
    }

    /** @test */
    public function it_throws_an_error_if_the_product_has_not_been_found()
    {
        $this->artisan("tracker:untrack 'Random Product'")
            ->assertExitCode(1);
    }
}
