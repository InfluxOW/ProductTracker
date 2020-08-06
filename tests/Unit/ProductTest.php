<?php

namespace Tests\Unit;

use App\Exceptions\ClientException;
use App\Product;
use App\Retailer;
use App\UseCases\TrackProduct;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use RetailerWithProductSeeder;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /** @test */
    public function it_can_be_tracked()
    {
        Bus::fake();

        Product::first()->track();

        Bus::assertDispatched(TrackProduct::class);
    }

    /** @test */
    public function it_throws_an_exception_if_a_client_is_not_found_when_tracking()
    {
        Retailer::first()->update(['name' => 'Fake Retailer']);

        $this->expectException(ClientException::class);

        Product::first()->track();
    }

    /** @test */
    public function it_updates_its_details_after_being_tracked()
    {
        Notification::fake();
        $this->mockCheckAvailabilityRequest($available = true, $price = 9900, $url = 'http://fake.test');

        $product = Product::first();
        $product->track();

        $this->assertEquals($available, $product->fresh()->in_stock);
        $this->assertEquals($price, $product->fresh()->price);
        $this->assertEquals($url, $product->fresh()->url);

    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RetailerWithProductSeeder::class);
    }
}
