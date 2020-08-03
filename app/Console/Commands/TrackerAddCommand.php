<?php

namespace App\Console\Commands;

use App\Exceptions\StockException;
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
        try {
            $retailer = $this->getRetailer(
                $this->argument('retailer') ?? $this->ask('Which retailer do you want to use?')
            );
            $product = Product::firstOrCreate(
                ['name' => $this->argument('product') ?? $this->ask('What product do you want to add?')]
            );
            $stock = Stock::firstOrMake([
                'sku' => (int) $this->argument('stock')[0],
                'url' => (string) $this->argument('stock')[1],
                'price' => (int) $this->argument('stock')[2],
                'in_stock' => (bool) $this->argument('stock')[3]
            ]);

            throw_if(
                is_null($stock->sku),
                new StockException("Stock should have a sku.")
            );

            $retailer->addStock($product, $stock);

            $this->info("Product {$product->name} has been tracked!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
