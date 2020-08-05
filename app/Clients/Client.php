<?php

namespace App\Clients;

use App\Clients\Helpers\ProductStatus;
use App\Clients\Helpers\SearchResults;
use App\Product;

interface Client
{
    /* Actions */
    public function checkAvailability(Product $product): ProductStatus;
    public function search($product, $options): SearchResults;

    /* Endpoints */
    public function productEndpoint(...$params): string;
    public function searchEndpoint(...$params): string;

    /* Getters */

    /*
     * Product attributes mapper for the specific API.
     * Should return an array like [product DB column name => Product API param name for this column]
     * */
    public function getProductAttributes(): array;

    /*
     * Search attributes mapper for the specific API.
     * Should return an array like [command search attribute => API search param for this attribute]
     * */
    public function getSearchAttributes(): array;
}
