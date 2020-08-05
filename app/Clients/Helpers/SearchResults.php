<?php

namespace App\Clients\Helpers;

class SearchResults
{
    public array $products;
    public SearchPagination $pagination;

    public function __construct(array $products, SearchPagination $pagination)
    {
        $this->products = $products;
        $this->pagination = $pagination;
    }
}
