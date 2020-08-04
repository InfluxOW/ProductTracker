<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Support\Facades\Notification;
use RetailerWithProductSeeder;
use Tests\TestCase;

class TrackerInitCommandTest extends TestCase
{
    /** @test */
    public function it_tracks_product_stock()
    {
        Notification::fake();
        $this->seed(RetailerWithProductSeeder::class);

        $this->assertFalse(Product::first()->in_stock);

        $this->mockClientRequest();

        $this->artisan('tracker:init');

        $this->assertTrue(Product::first()->in_stock);
    }
}
