<?php

namespace Tests\Unit;

use App\Product;
use App\Retailer;
use App\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /** @test */
    public function it_checks_stock_for_products_at_retailers()
    {
        $switch = Product::create(['name' => 'Nintendo Switch']);
        $bestBuy = Retailer::create(['name' => 'Best Buy']);

        $this->assertFalse($switch->inStock());

        $stock = new Stock([
            'price' => 10000,
            'url' => 'http://foobar.example',
            'sku' => 12345,
            'in_stock' => true
        ]);

        $bestBuy->addStock($switch, $stock);
        $this->assertTrue($switch->inStock());
    }
}
