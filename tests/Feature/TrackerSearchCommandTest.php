<?php

namespace Tests\Feature;

use App\Clients\Helpers\SearchPagination;
use App\Retailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use RetailersSeeder;

class TrackerSearchCommandTest extends TestCase
{
    protected function setUp():void
    {
        parent::setUp();

        $this->seed(RetailersSeeder::class);
        $this->search = 'Nintendo Switch';
        $this->retailer = Retailer::first();
        Http::fake(fn() => ['products' => [['sku' => 6364253, 'name' => $this->search]], 'totalPages' => 1, 'currentPage' => 1]);
    }

    /** @test */
    public function it_can_search_a_product()
    {
        $this->artisan("tracker:search '{$this->retailer->name}' '{$this->search}'")
            ->expectsConfirmation('Do you want to track one of the above products?', 'no')
            ->assertExitCode(0);
    }
}
