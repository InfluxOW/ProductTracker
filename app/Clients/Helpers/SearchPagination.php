<?php

namespace App\Clients\Helpers;

class SearchPagination
{
    public int $current;
    public int $total;

    public function __construct(int $current, int $total)
    {
        $this->current = $current;
        $this->total = $total;
    }

    public function __toArray()
    {
        return ['current_page' => $this->current, 'total_pages' => $this->total];
    }
}
