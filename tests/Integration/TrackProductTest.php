<?php

namespace Tests\Integration;

use App\History;
use App\Notifications\ImportantProductUpdate;
use App\Product;
use App\Stock;
use App\UseCases\TrackProduct;
use Illuminate\Support\Facades\Notification;
use RetailerWithProductSeeder;
use Tests\TestCase;

class TrackProductTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->mockClientRequest($available = true, $price = 24900);

        $this->seed(RetailerWithProductSeeder::class);

        Product::first()->track();
    }

    /** @test */
    public function it_notifies_the_user()
    {
        Notification::assertTimesSent(1, ImportantProductUpdate::class);
    }

    /** @test */
    public function it_refreshes_the_local_stock()
    {
        $this->assertDatabaseHas('products', [
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
