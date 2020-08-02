<?php

namespace App\Console\Commands;

use App\Stock;

class TrackerInitCommand extends Tracker
{
    protected $signature = 'tracker:init';
    protected $description = 'Track all products stock';

    public function handle()
    {
        $this->output->progressStart(Stock::count());

        Stock::each(function($stock) {
            $stock->track();
            $this->output->progressAdvance();
        });

        $this->output->progressFinish();

        $this->showResults();
    }

    protected function showResults()
    {
        $data = Stock::query()
            ->leftJoin('products', 'products.id', '=', 'stock.product_id')
            ->get($this->keys());

        $this->table(
            $this->keys(),
            $data
        );
    }

    protected function keys()
    {
        return ['name', 'price', 'sku', 'url', 'in_stock'];
    }
}
