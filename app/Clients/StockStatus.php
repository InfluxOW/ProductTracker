<?php

namespace App\Clients;

class StockStatus
{
    public $available;
    public $price;
    public $url;

    public function __construct(bool $available, int $price, string $url)
    {
        $this->available = $available;
        $this->price = $price;
        $this->url = $url;
    }

}
