<?php

namespace App\Clients;

use App\Clients\Helpers\StockStatus;
use App\Stock;

interface Client
{
    /* Actions */
    public function checkAvailability(Stock $stock): StockStatus;
    public function search($product, $options): array;

    /* Getters */
    public function getProductAttributes(): array;
    public function getSearchAttributes(): array;

    /* Endpoints */
    public function productEndpoint(...$params): string;
    public function searchEndpoint(...$params): string;
}
