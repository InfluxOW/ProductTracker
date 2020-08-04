<?php

namespace Tests\Integration;

use App\History;
use App\Notifications\ImportantProductUpdate;
use App\Stock;
use App\UseCases\TrackProduct;
use Illuminate\Support\Facades\Notification;
use RetailerWithProductSeeder;
use Tests\TestCase;

class TrackStockTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->mockClientRequest($available = true, $price = 24900);

        $this->seed(RetailerWithProductSeeder::class);

        (new TrackProduct(Stock::first()))->handle();
    }

    /** @test */
    public function it_notifies_the_user()
    {
        Notification::assertTimesSent(1, ImportantProductUpdate::class);
    }

    /** @test */
    public function it_refreshes_the_local_stock()
    {
        $this->assertDatabaseHas('stock', [
            'price' => 24900,
            'in_stock' => true
        ]);
    }

    /** @test */
    public function it_records_to_history()
    {
        $this->assertEquals(1, History::count());
    }
}
