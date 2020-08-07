<?php

namespace Tests\Feature;

use App\Product;
use App\Retailer;
use Illuminate\Support\Facades\Http;
use RetailersSeeder;
use Tests\TestCase;

class TrackerSearchCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RetailersSeeder::class);
        $this->search = 'Nintendo Switch';
        $this->retailer = Retailer::first();
        $this->product = ['sku' => 6364253, 'name' => $this->search];
        Http::fake(fn() => ['products' => [$this->product], 'totalPages' => 1, 'currentPage' => 1]);
    }

    /** @test */
    public function it_can_search_a_product()
    {
        $this->artisan("tracker:search '{$this->retailer->name}' '{$this->search}'")
            ->expectsConfirmation('Do you want to track one of the above products?', 'no')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_can_add_a_product_to_the_tracker_after_searching()
    {
        $this->assertEmpty(Product::all());

        $this->artisan("tracker:search '{$this->retailer->name}' '{$this->search}'")
            ->expectsConfirmation('Do you want to track one of the above products?', 'yes')
            ->expectsQuestion('Enter SKU of the product you want to track', $this->product['sku'])
            ->expectsConfirmation('Do you want to track anything else?', 'no')
            ->assertExitCode(0);

        $this->assertCount(1, Product::all());
    }
}
