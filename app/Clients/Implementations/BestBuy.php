<?php

namespace App\Clients\Implementations;

use App\Clients\Client;
use App\Clients\Helpers\ProductStatus;
use App\Clients\Helpers\SearchItem;
use App\Clients\Helpers\SearchResults;
use App\Console\Commands\Tracker;
use App\Exceptions\ApiException;
use App\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class BestBuy implements Client
{
    protected $key;

    public function __construct()
    {
        $this->key = config('services.clients.bestBuy.key');
    }

    public function checkAvailability(Product $product): ProductStatus
    {
        $results = Http::get($this->productEndpoint($product->sku))->json();

        if (array_key_exists('error', $results)) {
            throw new ApiException($results['error']['message']);
        }

        return new ProductStatus(
            $results['onlineAvailability'],
            $results['salePrice'] * 100,
            $results['url']
        );
    }

    public function search($input, $options): SearchResults
    {
        $results = Http::get($this->searchEndpoint($input, $options))->json();

        $products = [];
        foreach ($results['products'] as $product) {
            $products[] = replaceKeysWithMapper($product, array_flip($this->getProductAttributes()));
        }
        $pagination = ['currentPage' => $results['currentPage'], 'totalPages' => $results['totalPages']];

        return new SearchResults($products, $pagination);
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
            'show' => $options['show'],
            'sort' => $options['sort'],
            'pageSize' => $options['pageSize'],
            'page' => $options['page'],
            'apiKey' => $this->key
        ]);

        $search = trim(
            Str::of($input)
                ->explode(' ')
                ->map(function($searchTerm) {
                    return Str::start($searchTerm, "search=");
                })
                ->add($options['filters'])
                ->implode('&'),
            '&');

        return "https://api.bestbuy.com/v1/products({$search})?{$query}";
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
            'per' => 'pageSize',
            'page' => 'page',
            'filters' => 'filters',
            'sort' => 'sort',
            'attributes' => 'show',
        ];
    }
}
