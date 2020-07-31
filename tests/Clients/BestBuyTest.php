<?php

namespace Tests\Clients;

use App\Stock;
use App\Clients\BestBuy;
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

        $stock = Stock::first();
        $stock->update([
            'sku' => '6364253', // Nintendo Switch SKU
            'url' => 'https://www.bestbuy.com/site/nintendo-switch-32gb-console-gray-joy-con/6364253.p?skuId=6364253'
        ]);

        try {
            (new BestBuy())->checkAvailability($stock);
        } catch (\Exception $e) {
            $this->fail('Failed to track the BestBuy API properly.' . $e->getMessage());
        }
    }

    /** @test */
    public function it_creates_a_proper_stock_status_response()
    {
        Http::fake(fn() => ['onlineAvailability' => true, 'salePrice' => 299.99]);
        $stockStatus = (new BestBuy())->checkAvailability(new Stock());

        $this->assertEquals(29999, $stockStatus->price);
        $this->assertTrue($stockStatus->available);
    }
}
