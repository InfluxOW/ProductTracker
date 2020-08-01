<?php

namespace App\Console\Commands;

use App\Exceptions\ClientException;
use App\Exceptions\ProductException;
use App\Exceptions\RetailerException;
use App\Product;
use App\Retailer;
use App\Stock;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TrackerSearchCommand extends Command
{
    protected $signature = 'tracker:search
    { retailer? : Retailer you want to use }
    { product? : Name of the product you are looking for }
    { --perPage=20 : Items per page <1-100> }
    { --page=1 : Current search page }
    { --filters= : Filter results by any params (e.g. `onlineAvailability=true`) }
    { --sort=salePrice.asc : Sort results by any params }
    { --showAttributes=sku,name,salePrice,onlineAvailability,url : Product attributes that you want to receive }';

    protected $description = 'Search for product in the selected retailer stock';
    protected $userInput = [];
    protected $retailer;

    public function handle()
    {
        $this->setRetailer();
        $this->userInput['product'] = $this->argument('product') ?? $this->ask('What product are you looking for?');

        [$products, $pages] = $this->retailer->client()->search(
            $this->userInput['product'],
            $this->getSearchOptions()
        );

        $this->displayResults($products, $pages);

        if ($this->confirm('Do you want to track one of the above results?')) {
            $this->track($products);
        }
    }

    protected function setRetailer()
    {
        $this->userInput['retailer'] = $this->argument('retailer') ?? $this->ask('Which retailer do you want to use?');
        $retailer = Retailer::all()->filter(function($retailer) {
            return $this->retailersMatches($retailer);
        })->first();

        throw_if(
            is_null($retailer),
            new RetailerException("Retailer {$this->userInput['retailer']} has not been found.")
        );

        $this->retailer = $retailer;
    }

    protected function retailersMatches($retailer): bool
    {
        return Str::lower(Str::studly($this->userInput['retailer'])) ===
            Str::lower(Str::studly($retailer->name));
    }

    protected function getSearchOptions()
    {
        return [
            'perPage' => $this->option('perPage'),
            'page' => $this->option('page'),
            'showAttributes' => $this->option('showAttributes'),
            'filters' => $this->option('filters'),
            'sort' => $this->option('sort')
        ];
    }

    protected function displayResults($products, $pages)
    {
        $this->table(
            array_keys($products[0]),
            $products
        );
        $this->info(json_encode($pages));
    }

    protected function track($products)
    {
        $sku = $this->ask('Type SKU of the product you want to track');
        $item = collect($products)
            ->filter(function ($item) use ($sku) {
                return $item['sku'] == $sku;
            })
            ->first();

        throw_if(is_null($item), new ProductException("Product with SKU {$sku} has not been found in the search results"));

        $product = Product::firstOrCreate(['name' => $item['name']]);
        $stock = Stock::findOrMake([
            'price' => $item['salePrice'],
            'url' => $item['url'],
            'sku' => $item['sku'],
            'in_stock' => $item['onlineAvailability']
        ]);
        $this->retailer->addStock($product, $stock);
        $this->info("Product {$product->name} has been tracked!");
    }
}
