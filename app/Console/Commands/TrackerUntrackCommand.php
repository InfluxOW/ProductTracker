<?php

namespace App\Console\Commands;

use App\Exceptions\ProductException;
use App\Product;

class TrackerUntrackCommand extends Tracker
{
    protected $signature = 'tracker:untrack
    { product : Full name or first letters of the product you want to untrack [register matters] }';
    protected $description = 'Untrack any product';

    public function handle()
    {
        try {
            $product = Product::where('name', 'like', "{$this->argument('product')}%");

            throw_if(
                $product->doesntExist(),
                new ProductException("Product '{$this->argument('product')}' has not been found.")
            );

            $product->first()->stock->each->delete();
            $this->info("Selected product has been untracked!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
