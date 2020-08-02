<?php

namespace App\Clients\Helpers;

class StockStatus
{
    public $available;
    public $price;
    public $url;

    public function __construct(bool $available, int $price, string $url = null)
    {
        $this->available = $available;
        $this->price = $price;
        $this->url = $url;
    }

}
