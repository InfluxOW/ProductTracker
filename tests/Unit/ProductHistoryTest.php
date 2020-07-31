<?php

namespace Tests\Unit;

use App\History;
use App\Product;
use RetailerWithProductSeeder;
use Tests\TestCase;

class ProductHistoryTest extends TestCase
{
    /** @test */
    public function it_records_history_each_time_stock_tracked()
    {
        $this->seed(RetailerWithProductSeeder::class);
        $this->mockClientRequest();

        $product = Product::first();
        $this->assertEmpty($product->history);
        $product->track();
        $this->assertCount(1, $product->fresh()->history);

        $stock = $product->stock->first();
        $history = $product->fresh()->history->first();
        $this->assertEquals($stock->price, $history->price);
        $this->assertEquals($stock->in_stock, $history->in_stock);
        $this->assertEquals($stock->product_id, $history->product_id);
        $this->assertEquals($stock->id, $history->stock_id);

    }
}
