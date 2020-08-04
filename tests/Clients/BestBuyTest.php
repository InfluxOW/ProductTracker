<?php

namespace Tests\Clients;

use App\Product;
use App\Clients\Implementations\BestBuy;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use RetailerWithProductSeeder;

/**
 * @group api
 */
class BestBuyTest extends TestCase
{

    /** @test
     *  @doesNotPerformAssertions
     */
    public function it_tracks_a_product()
    {
        $this->seed(RetailerWithProductSeeder::class);

        try {
            (new BestBuy())->checkAvailability(Product::first());
        } catch (\Exception $e) {
            $this->fail('Failed to track the BestBuy API properly.' . $e->getMessage());
        }
    }

    /** @test */
    public function it_creates_a_proper_stock_status_response()
    {
        Http::fake(fn() => ['onlineAvailability' => true, 'salePrice' => 299.99, 'url' => '']);
        $productStatus = (new BestBuy())->checkAvailability(new Product());

        $this->assertEquals(29999, $productStatus->price);
        $this->assertTrue($productStatus->available);
    }
}
