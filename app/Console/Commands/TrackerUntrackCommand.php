<?php

namespace App\Console\Commands;

use App\Exceptions\ProductException;
use App\Product;

class TrackerUntrackCommand extends Tracker
{
    protected $signature = 'tracker:untrack
    { product? : Full name or first letters of the product you want to untrack [register matters] }';
    protected $description = 'Untrack any product';

    public function isHidden()
    {
        return false;
    }

    public function handle()
    {
        $name = $this->argument('product') ?? $this->ask('Enter full name or first letters of the product you want to untrack [register matters]');
        $product = Product::where('name', 'like', "{$name}%");

        throw_if(
            $product->doesntExist(),
            new ProductException("Product '{$name}' has not been found.")
        );

        $product->first()->delete();
        $this->info("Selected product has been untracked!");
    }
}
