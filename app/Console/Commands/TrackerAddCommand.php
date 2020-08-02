<?php

namespace App\Console\Commands;

use App\Product;
use App\Stock;

class TrackerAddCommand extends Tracker
{
    protected $signature = 'tracker:add
    { product? : Name of the product you want to track }
    { retailer? : Name of the retailer you want to use }
    { stock?* : Stock details [required: sku, optional: url, price, in_stock] }';
    protected $description = 'Add new item to the tracker';

    public function handle()
    {
        $retailer = $this->getRetailer($this->argument('retailer'));
        $product = Product::firstOrCreate(['name' => $this->argument('product')]);
        $stock = Stock::firstOrMake([
            'price' => (int) $this->argument('stock')[2],
            'url' => (string) $this->argument('stock')[1],
            'sku' => (int) $this->argument('stock')[0],
            'in_stock' => (bool) $this->argument('stock')[3]
        ]);
        $retailer->addStock($product, $stock);

        $this->info("Product {$product->name} has been tracked!");
    }
}
