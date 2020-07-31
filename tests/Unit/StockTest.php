<?php

namespace Tests\Unit;

use App\Clients\Client;
use App\Clients\StockStatus;
use App\Exceptions\ClientException;
use App\Retailer;
use App\Stock;
use Facades\App\Clients\ClientFactory;
use RetailerWithProductSeeder;
use Tests\TestCase;

class StockTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_a_client_is_not_found_when_tracking()
    {
        $this->seed(RetailerWithProductSeeder::class);
        Retailer::first()->update(['name' => 'Fake Retailer']);

        $this->expectException(ClientException::class);

        Stock::first()->track();
    }

    /** @test */
    public function it_updates_local_stock_status_after_being_tracked()
    {
        $this->seed(RetailerWithProductSeeder::class);
        $this->mockClientRequest($available = true, $price = 9900);

        $stock = Stock::first();
        $stock->track();

        $this->assertTrue($stock->in_stock);
        $this->assertEquals($price, $stock->price);

    }
}
