<?php

namespace Tests;

use App\Clients\Helpers\ProductStatus;
use App\Clients\Helpers\SearchResults;
use Facades\App\Clients\Implementations\BestBuy;
use Facades\App\Clients\ClientFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function mockCheckAvailabilityRequest($available = true, $price = 29900, $url = null)
    {
        ClientFactory::shouldReceive('make->checkAvailability')
            ->andReturn(new ProductStatus($available, $price, $url));
    }
}
