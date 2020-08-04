<?php

namespace Tests\Unit;

use App\History;
use App\Product;
use Illuminate\Support\Facades\Notification;
use RetailerWithProductSeeder;
use Tests\TestCase;

class ProductHistoryTest extends TestCase
{
    /** @test */
    public function it_records_history_each_time_stock_tracked()
    {
        Notification::fake();
        $this->seed(RetailerWithProductSeeder::class);
        $this->mockClientRequest();

        $product = Product::first();
        $this->assertEmpty($product->history);
        $product->track();
        $this->assertCount(1, $product->fresh()->history);

        $history = $product->fresh()->history->first();
        $this->assertEquals($product->fresh()->price, $history->price);
        $this->assertEquals($product->fresh()->in_stock, $history->in_stock);
        $this->assertEquals($product->fresh()->id, $history->product_id);

    }
}
