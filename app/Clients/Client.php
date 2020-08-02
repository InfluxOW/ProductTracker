<?php

namespace App\Clients;

use App\Clients\Helpers\StockStatus;
use App\Stock;

interface Client
{
    public function checkAvailability(Stock $stock): StockStatus;
    public function search($product, $options);
}
