<?php

namespace App\Clients\Helpers;

use Illuminate\Database\Eloquent\Collection;

class SearchResults
{
    public Collection $products;
    public array $pagination;

    public function __construct(Collection $products, array $pagination)
    {
        $this->products = $products;
        $this->pagination = $pagination;
    }
}
