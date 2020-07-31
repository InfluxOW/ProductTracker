<?php

namespace Tests\Unit;

use App\Exceptions\ClientException;
use App\Retailer;
use App\Stock;
use Tests\TestCase;

class StockTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_if_a_client_is_not_found_when_tracking()
    {
        $this->seed(\RetailerWithProductSeeder::class);
        Retailer::first()->update(['name' => 'Fake Retailer']);

        $this->expectException(ClientException::class);

        Stock::first()->track();
    }
}
