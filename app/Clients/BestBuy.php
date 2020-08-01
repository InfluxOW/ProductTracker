<?php

namespace App\Clients;

use App\Stock;
use Illuminate\Support\Facades\Http;

class BestBuy implements Client
{
    protected $key;

    public function __construct()
    {
        $this->key = config('services.clients.bestBuy.key');
    }

    public function checkAvailability(Stock $stock): StockStatus
    {
        $results = Http::get($this->endpoint($stock->sku))->json();

        return new StockStatus(
            $results['onlineAvailability'],
            $results['salePrice'] * 100,
            $results['url']
        );
    }

    protected function endpoint($sku): string
    {
        return "https://api.bestbuy.com/v1/products/{$sku}.json?apiKey={$this->key}";
    }
}
