<?php

namespace App\Console\Commands;

use App\Exceptions\ProductException;
use App\Retailer;

class TrackerSearchCommand extends Tracker
{
    protected $signature = 'tracker:search
    { retailer? : Retailer you want to use. Use `tracker:retailers` to check available retailers. }
    { product? : Name of the product you are looking for. }
    { --per=20 : Items per page <1-100> }
    { --page=1 : Current search page }
    { --filters= : Filter results by any params (e.g. `in_stock=true`) }
    { --sort=price.asc : Sort results by any params }
    { --attributes=sku,name,price,in_stock,url : Product attributes that you want to receive }';
    protected $description = 'Search for a product in the stock of the selected retailer';

    protected string $product;
    protected Retailer $retailer;

    public function handle()
    {
        $this->setInitialData();

        [$products, $pages] = $this->getSearchResults();

        $this->displayResults($products, $pages);

        $this->startTracking($products);
    }

    protected function setInitialData()
    {
        $this->retailer = $this->getRetailer(
            $this->argument('retailer') ?? $this->choice('Which retailer do you want to use?', $this->retailers())
        );

        $this->product = $this->argument('product') ?? $this->ask('What product are you looking for?');
    }

    protected function getSearchResults()
    {
        return $this->retailer->client()->search(
            $this->product,
            $this->transformSearchOptions()
        );
    }

    protected function transformSearchOptions()
    {
        $optionsWithCorrectKeys = replaceKeysWithMapper($this->options(), $this->retailer->client()->getSearchAttributes());
        return replaceValuesWithMapper($optionsWithCorrectKeys, $this->retailer->client()->getProductAttributes());
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
        $sku = $this->askWithValidation('Enter SKU of the product you want to track', 'sku', $this->productValidationRules()['sku']);
        $item = collect($products)->firstWhere('sku', '==', $sku);

        throw_if(
            is_null($item),
            new ProductException("Product with SKU {$sku} has not been found in the search results")
        );

        return $item;
    }

    protected function track($item)
    {
        $product = $this->transformApiProductAttributesToDbAttributes($item);

        $this->call('tracker:add', [
            'retailer' => $this->retailer->name,
            'product' => [
                $product['name'],
                $product['sku'],
                $product['url'],
                $product['price'],
                $product['in_stock'],
            ]
        ]);
    }
}
