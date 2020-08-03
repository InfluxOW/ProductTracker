<?php

namespace App\Clients;

use App\Clients\Helpers\StockStatus;
use App\Stock;

interface Client
{
    /* Actions */
    public function checkAvailability(Stock $stock): StockStatus;
    public function search($product, $options): array;

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
