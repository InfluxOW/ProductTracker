<?php

namespace App\Console\Commands;

use App\Exceptions\ProductException;
use App\Exceptions\RetailerException;
use App\Product;
use App\Retailer;

class TrackerSearchCommand extends Tracker
{
    protected $signature = 'tracker:search
    { retailer? : Retailer you want to use. Use `tracker:retailers` to check available retailers. }
    { product? : Name of the product you are looking for. }
    { --per=20 : Items per page <1-100> }
    { --page=1 : Current search page }
    { --filters= : Filter results by any params (e.g. `onlineAvailability=true`) }
    { --sort=price.asc : Sort results by any params }
    { --attributes=sku,name,price,in_stock,url : Product attributes that you want to receive }';
    protected $description = 'Search for a product in the stock of the selected retailer';

    protected string $product;
    protected Retailer $retailer;
    protected array $options;

    public function handle()
    {
        $this->setInitialData();

        [$products, $pages] = $this->getSearchResults();

        $this->displayResults($products, $pages);

        $this->startTracking($products);
    }

    protected function setInitialData()
    {
        $this->options = $this->options();

        $this->retailer = $this->getRetailer(
            $this->argument('retailer') ?? $this->ask('Which retailer do you want to use?')
        );

        $this->product = $this->argument('product') ?? $this->ask('What product are you looking for?');
    }

    protected function getSearchResults()
    {
        return $this->retailer->client()->search(
            $this->product,
            $this->getSearchOptions()
        );
    }

    protected function getSearchOptions()
    {
        $this->replaceOptionsKeys()->replaceOptionsValues();

        return $this->options;
    }

    protected function replaceOptionsKeys()
    {
        $attributes = $this->retailer->client()->getSearchAttributes();

        foreach ($this->options as $key => $value) {
            unset($this->options[$key]);

            if (array_key_exists($key, $attributes)) {
                $this->options[$attributes[$key]] = $value;
            }
        }

        return $this;
    }

    protected function replaceOptionsValues()
    {
        $attributes = $this->retailer->client()->getProductAttributes();

        foreach ($this->options as $key => $value) {
            foreach ($attributes as $option => $attribute) {
                if (! is_null($value) && str_contains($value, $option)) {
                    $this->options[$key] = str_replace($option, $attribute, $this->options[$key]);
                }
            }
        }

        return $this;
    }

    protected function displayResults($products, $pages)
    {
        $this->table(
            array_keys($products[0]),
            $products
        );
        $this->info(json_encode($pages));
    }

    protected function startTracking($products)
    {
        if ($this->confirm('Do you want to track one of the above results?')) {
            $this->track($this->getItemToTrack($products));

            while ($this->confirm('Do you want to track anything else?')) {
                $this->track($this->getItemToTrack($products));
            }
        }
    }

    protected function getItemToTrack($products)
    {
        $sku = $this->ask('Enter SKU of the product you want to track');
        $item = collect($products)->firstWhere('sku', '==', $sku);

        throw_if(
            is_null($item),
            new ProductException("Product with SKU {$sku} has not been found in the search results")
        );

        return $item;
    }

    protected function track($item)
    {
        $product = Product::firstOrCreate(['name' => $item['name']]);
        $stock = $this->createStock($item);

        $this->call('tracker:add', [
            'product' => $product->name,
            'retailer' => $this->retailer->name,
            'stock' => [
                $stock->sku,
                $stock->url,
                $stock->price,
                $stock->in_stock
            ]
        ]);
    }

    protected function createStock(array $item): Stock
    {
        $attributes = $this->retailer->client()->getProductAttributes();

        foreach ($attributes as $key => $attribute) {
            foreach ($item as $param => $value) {
                if ($attribute === $param) {
                    unset($item[$param]);
                    $item[$key] = $value;
                }
            }
        }

        return Stock::firstOrMake($item);
    }
}
