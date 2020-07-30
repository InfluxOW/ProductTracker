<?php

namespace Tests\Feature;

use App\Product;
use App\Retailer;
use App\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrackCommandTest extends TestCase
{
    /** @test */
    public function it_tracks_product_stock()
    {
        $switch = Product::create(['name' => 'Nintendo Switch']);
        $bestBuy = Retailer::create(['name' => 'Best Buy']);

        $this->assertFalse($switch->inStock());

        $stock = new Stock([
            'price' => 10000,
            'url' => 'http://foobar.example',
            'sku' => 12345,
            'in_stock' => false
        ]);

        $this->assertFalse($stock->in_stock);
        $bestBuy->addStock($switch, $stock);

        Http::fake(function () {
            return [
                'available' => true,
                'price' => 29900
            ];
        });
        $this->artisan('track');
        $this->assertTrue($stock->fresh()->in_stock);
    }
}
