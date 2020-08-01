<?php

namespace App\Console\Commands;

use App\Product;
use Illuminate\Console\Command;

class TrackerInitCommand extends Command
{
    protected $signature = 'tracker:init';
    protected $description = 'Track all product stock';

    public function handle()
    {
        $products = Product::all();

        $this->output->progressStart($products->count());

        $products->each(function ($product) {
            $product->track();
            $this->output->progressAdvance();
        });

        $this->output->progressFinish();

        $this->showResults();
    }

    protected function showResults()
    {
        $data = Product::query()
            ->leftJoin('stock', 'stock.product_id', '=', 'products.id')
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
