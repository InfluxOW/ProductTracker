<?php

namespace App\UseCases;

use App\Clients\Helpers\StockStatus;
use App\History;
use App\Notifications\ImportantStockUpdate;
use App\Stock;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TrackStock implements ShouldQueue
{
    use Dispatchable;

    protected Stock $stock;
    protected StockStatus $status;

    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    public function handle()
    {
        $this->checkAvailability();

        $this->refreshStock();
        $this->notifyUser();
        $this->recordToHistory();
    }

    protected function checkAvailability()
    {
        $this->status = $this->stock->retailer
            ->client()
            ->checkAvailability($this->stock);
    }

    protected function notifyUser()
    {
        if ($this->isNowInStock()) {
            User::first()->notify(
                new ImportantStockUpdate($this->stock)
            );
        }

    }

    protected function isNowInStock()
    {
        return !$this->stock->in_stock && $this->status->available;
    }

    protected function refreshStock()
    {
        $this->stock->update([
            'in_stock' => $this->status->available,
            'price' => $this->status->price,
            'url' => $this->status->url
        ]);
    }

    protected function recordToHistory()
    {
        History::create([
            'price' => $this->stock->price,
            'in_stock' => $this->stock->in_stock,
            'stock_id' => $this->stock->id,
            'product_id' => $this->stock->product_id,
        ]);
    }
}
