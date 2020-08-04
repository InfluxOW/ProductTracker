<?php

namespace App\Console\Commands;

use App\Exceptions\ProductException;
use App\Exceptions\StockException;
use App\Product;

class TrackerAddCommand extends Tracker
{
    protected $signature = 'tracker:add
    { retailer? : Name of the retailer you want to use }
    { product?* : Product details [required: name, sku; optional: url, price, in_stock]}';
    protected $description = 'Add new product to the tracker';

    public function handle()
    {
        try {
            $product = $this->getProduct();

            $this->info("Product {$product->name} has been tracked!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function getProduct()
    {
        $retailer = $this->getRetailer(
            $this->argument('retailer') ?? $this->ask('Which retailer do you want to use?')
        );
        $productAttributes = empty($this->argument('product')) ? $this->askAboutProduct() : $this->getProductAttributesFromCommandArguments();

        $product = Product::firstOrMake($productAttributes);
        $product->retailer()->associate($retailer);
        $product->save();

        $this->validate($product);

        return $product;
    }

    protected function askAboutProduct()
    {
        $attributes['name'] =  $this->ask('What product do you want to add?');
        $attributes['sku'] = $this->ask('Enter SKU of the product');

        if ($this->confirm('Do you want to add any additional product information?')) {
            $attributes['url'] = $this->ask('Enter url of the product');
            $attributes['price'] = $this->ask('Enter price of the product');
            $attributes['in_stock'] = $this->confirm('Is product in stock?');
        }

        return $attributes;
    }


    protected function getProductAttributesFromCommandArguments()
    {
        return [
            'name' => $this->argument('stock')[0],
            'sku' => $this->argument('stock')[1],
            'url' => $this->argument('stock')[2],
            'price' => $this->argument('stock')[3],
            'in_stock' => $this->argument('stock')[4]
        ];
    }

    protected function validate(Product $product)
    {
        throw_if(
            is_null($product->name),
            new ProductException("Product should not have an empty name.")
        );

        throw_if(
            is_null($product->sku),
            new ProductException("Product should not have an empty sku.")
        );

        throw_if(
            $product->sku === 0,
            new ProductException("SKU should be greater than 0.")
        );

        throw_if(
            $product->price === 0,
            new ProductException("Price should be greater than 0.")
        );
    }
}
