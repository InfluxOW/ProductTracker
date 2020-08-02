<?php

namespace App\Console\Commands;

use App\Exceptions\StockException;
use App\Product;
use App\Stock;
use Illuminate\Console\Command;

class TrackerUntrackCommand extends Command
{
    protected $signature = 'tracker:untrack
    {product : Product name or SKU of associated stock}';
    protected $description = 'Untrack any product or one its stock';

    public function handle()
    {
        $input = $this->argument('product');

        $product = Product::where('name', $input)->first();
        if (! is_null($product)) {
            $product->stock->each->delete();
            $this->info("Selected product has been untracked!");
            return;
        }

        $stock = Stock::where('sku', $input)->first();
        if (! is_null($stock)) {
            $stock->delete();
            $this->info("Selected stock has been untracked!");
            return;
        }

        $this->error("Nothing has been found via your request...");
    }
}
