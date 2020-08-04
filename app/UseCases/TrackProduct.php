<?php

namespace App\UseCases;

use App\Clients\Helpers\ProductStatus;
use App\History;
use App\Notifications\ImportantProductUpdate;
use App\Product;
use App\Stock;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TrackProduct implements ShouldQueue
{
    use Dispatchable;

    protected Product $product;
    protected ProductStatus $status;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        $this->checkAvailability();

        $this->notifyUser();
        $this->refreshStock();
        $this->recordToHistory();
    }

    protected function checkAvailability()
    {
        $client = $this->product->retailer->client();

        $this->status = $client->checkAvailability($this->product);
    }

    protected function notifyUser()
    {
        if ($this->isNowInStock()) {
            User::first()->notify(
                new ImportantProductUpdate($this->product)
            );
        }

    }

    protected function isNowInStock()
    {
        return ! $this->product->in_stock && $this->status->available;
    }

    protected function refreshStock()
    {
        $this->product->update([
            'in_stock' => $this->status->available,
            'price' => $this->status->price,
            'url' => $this->status->url
        ]);
    }

    protected function recordToHistory()
    {
        $history = History::make([
            'price' => $this->product->price,
            'in_stock' => $this->product->in_stock,
        ]);
        $this->product->history()->save($history);
    }
}
