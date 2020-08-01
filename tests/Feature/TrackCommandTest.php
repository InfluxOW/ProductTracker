<?php

namespace Tests\Feature;

use App\Product;
use Illuminate\Support\Facades\Notification;
use RetailerWithProductSeeder;
use Tests\TestCase;

class TrackCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->seed(RetailerWithProductSeeder::class);
    }

    /** @test */
    public function it_tracks_product_stock()
    {
        $this->assertFalse(Product::first()->inStock());

        $this->mockClientRequest();

        $this->artisan('track')
            ->expectsOutput('All done!');

        $this->assertTrue(Product::first()->inStock());
    }
}
