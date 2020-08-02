<?php

namespace App\Clients\Implementations;

use App\Clients\Client;
use App\Clients\Helpers\SearchResults;
use App\Clients\Helpers\StockStatus;
use App\Stock;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BestBuy implements Client
{
    protected $key;

    public function __construct()
    {
        $this->key = config('services.clients.bestBuy.key');
    }

    public function checkAvailability(Stock $stock): StockStatus
    {
        $results = Http::get($this->productEndpoint($stock->sku))->json();

        return new StockStatus(
            $results['onlineAvailability'],
            $results['salePrice'] * 100,
            $results['url'] ?? null
        );
    }

    public function search($input, $options): array
    {
        $results = Http::get($this->searchEndpoint($input, $options))->json();

        $products = $results['products'];
        $pages = ['currentPage' => $results['currentPage'],  'totalPages' => $results['totalPages']];

        return [$products, $pages];

        return new SearchResults($results);
    }

    public function getProductAttributes(): array
    {
        return [
            'sku' => 'sku',
            'name' => 'name',
            'price' => 'salePrice',
            'url' => 'url',
            'in_stock' => 'onlineAvailability',
        ];
    }

    public function getSearchAttributes(): array
    {
        return [
            'per' => 'perPage',
            'page' => 'page',
            'filters' => 'filters',
            'sort' => 'sort',
            'attributes' => 'showAttributes',
        ];
    }

    public function productEndpoint(...$params): string
    {
        [$sku] = $params;

        return "https://api.bestbuy.com/v1/products/{$sku}.json?apiKey={$this->key}";
    }

    public function searchEndpoint(...$params): string
    {
        [$input, $options] = $params;

        $query = http_build_query([
            'format' => 'json',
            'show' => $options['showAttributes'],
            'sort' => $options['sort'],
            'pageSize' => $options['perPage'],
            'page' => $options['page'],
            'apiKey' => $this->key
        ]);

        $search = trim(
                Str::of($input)
                    ->explode(' ')
                    ->map(function ($searchTerm) {
                        return Str::start($searchTerm, "search=");
                    })
                    ->add($options['filters'])
                    ->implode('&'),
            '&');

        return "https://api.bestbuy.com/v1/products({$search})?{$query}";
    }
}
