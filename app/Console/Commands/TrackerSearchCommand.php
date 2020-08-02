<?php

namespace App\Console\Commands;

use App\Exceptions\ProductException;
use App\Exceptions\RetailerException;
use App\Product;
use App\Retailer;
use App\Stock;
use Illuminate\Console\Command;

class TrackerSearchCommand extends Command
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

    protected $userInput;
    protected $retailer;
    protected $options;

    public function handle()
    {
        try {
            $this->setInitialData();

            [$products, $pages] = $this->getSearchResults();

            $this->displayResults($products, $pages);

            $this->startTracking($products);

            $this->line('Thank you for using the app!');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function setInitialData()
    {
        $this->options = $this->options();

        $this->userInput['retailer'] = $this->argument('retailer') ?? $this->ask('Which retailer do you want to use?');
        $this->retailer = $this->getRetailer();

        $this->userInput['product'] = $this->argument('product') ?? $this->ask('What product are you looking for?');
    }

    protected function getRetailer()
    {
        $retailer = Retailer::all()
            ->filter(function($retailer) {
                return toLowercaseWord($this->userInput['retailer']) === toLowercaseWord($retailer->name);
            })->first();

        throw_if(
            is_null($retailer),
            new RetailerException("Retailer {$this->userInput['retailer']} has not been found.")
        );

        return $retailer;
    }

    protected function getSearchResults()
    {
        return $this->retailer->client()->search(
            $this->userInput['product'],
            $this->getSearchOptions()
        );
    }

    protected function getSearchOptions()
    {
        $this->replaceOptionsKeys()->replaceOptionsValues();

        return $this->options;
    }

    protected function replaceOptionsValues()
    {
        $attributes = $this->retailer->client()->getProductAttributes();

        foreach ($this->options as $key => $value) {
            foreach ($attributes as $option => $attribute) {
                if (str_contains($value, $option)) {
                    $this->options[$key] = str_replace($option, $attribute, $this->options[$key]);
                }
            }
        }

        return $this;
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

    protected function track($item)
    {
        $product = Product::firstOrCreate(['name' => $item['name']]);

        $stock = Stock::firstOrMake([
            'price' => $item['salePrice'],
            'url' => $item['url'],
            'sku' => $item['sku'],
            'in_stock' => $item['onlineAvailability']
        ]);
        $this->retailer->addStock($product, $stock);

        $this->info("Product {$product->name} has been tracked!");
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
}
