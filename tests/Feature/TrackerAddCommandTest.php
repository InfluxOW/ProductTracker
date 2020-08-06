<?php

namespace Tests\Feature;

use App\Product;
use App\Retailer;
use RetailersSeeder;
use Tests\TestCase;

class TrackerAddCommandTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();
        $this->seed(RetailersSeeder::class);
        $this->retailer = Retailer::first();
        $this->product = [
            'name' => 'Nintendo Switch',
            'sku' => 6364253,
            'url' => 'http://test.com',
            'price' => 2999,
            'in_stock' => true
        ];
    }

    /** @test */
    public function it_can_add_a_product_to_the_tracker_with_command_arguments_specified()
    {
        $this->assertEmpty(Product::all());

        $this->artisan("tracker:add {$this->retailer->name} '{$this->product['name']}' {$this->product['sku']}")
            ->assertExitCode(0);

        $this->assertEquals(1, Product::count());
    }

    /** @test */
    public function it_can_add_a_product_to_the_tracker_with_command_arguments_unspecified()
    {
        $this->assertEmpty(Product::all());

        $this->artisan('tracker:add')
            ->expectsChoice('Which retailer do you want to use?', $this->retailer->name, [$this->retailer->name])
            ->expectsQuestion('What product do you want to add?', $this->product['name'])
            ->expectsQuestion('Enter SKU of the product', $this->product['sku'])
            ->expectsConfirmation('Do you want to add any additional product information?', 'yes')
            ->expectsQuestion('Enter url of the product', $this->product['url'])
            ->expectsQuestion('Enter price of the product in cents', $this->product['price'])
            ->expectsConfirmation('Is product in stock?', $this->product['in_stock'] ? 'yes' : 'no')
            ->assertExitCode(0);

        $this->assertEquals(1, Product::count());
    }

    /** @test */
    public function it_fails_with_invalid_command_arguments()
    {
        $this->artisan("tracker:add {$this->retailer->name} '{$this->product['name']}' 'invalid sku'")
            ->assertExitCode(1);
    }
}
