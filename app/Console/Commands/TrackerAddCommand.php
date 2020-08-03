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
            $stock = $this->getStock();

            $retailer->addStock($product, $stock);

            $this->info("Product {$product->name} has been tracked!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function getStock()
    {
        $stockAttributes = empty($this->argument('stock')) ? $this->askAboutStock() : $this->getStockAttributesFromCommandArguments();

        $stock = Stock::firstOrMake($stockAttributes);

        throw_if(
            is_null($stock->sku),
            new StockException("Stock should have a sku.")
        );

        throw_if(
            $stock->sku === 0,
            new StockException("SKU should be greater than 0.")
        );

        throw_if(
            $stock->price === 0,
            new StockException("Price should be greater than 0.")
        );

        return $stock;
    }

    protected function askAboutStock()
    {
        $attributes['sku'] = $this->ask('Enter SKU of the product');

        if ($this->confirm('Do you want to add any additional product information?')) {
            $attributes['url'] = $this->ask('Enter url of the product');
            $attributes['price'] = $this->ask('Enter price of the product');
            $attributes['in_stock'] = $this->confirm('Is product in stock?');
        }

        return $attributes;
    }


    protected function getStockAttributesFromCommandArguments()
    {
        return [
            'sku' => $this->argument('stock')[0],
            'url' => $this->argument('stock')[1],
            'price' => $this->argument('stock')[2],
            'in_stock' => $this->argument('stock')[3]
        ];
    }
}
