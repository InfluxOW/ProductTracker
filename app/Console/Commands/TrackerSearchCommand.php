<?php

namespace App\Console\Commands;

use App\Exceptions\ClientException;
use App\Exceptions\RetailerException;
use App\Retailer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TrackerSearchCommand extends Command
{
    protected $signature = 'tracker:search
    { retailer? : Retailer you want to search with }
    { product? : Name of the product you are looking for }
    { --perPage=20 : Items per page <1-100> }
    { --page=1 : Current search page }
    { --filters= : Filter results by any params (e.g. `onlineAvailability=true`) }
    { --sort=salePrice.asc : Sort results by any params }
    { --showAttributes=sku,name,salePrice,onlineAvailability : Product attributes that you want to receive }';
    protected $description = 'Search for product in the selected retailer stock';
    protected $userInput = [];

    public function handle()
    {
        $retailer = $this->getRetailer();
        $this->userInput['product'] = $this->argument('product') ?? $this->ask('What product are you looking for?');

        $results = $retailer->client()->search(
            $this->userInput['product'],
            $this->getSearchOptions()
        );

        $this->displayResults($results);

    }

    protected function getRetailer()
    {
        $this->userInput['retailer'] = $this->argument('retailer') ?? $this->ask('Which retailer do you want to use?');
        $retailer = Retailer::all()->filter(function($retailer) {
            return $this->retailersMatches($retailer);
        })->first();

        throw_if(
            is_null($retailer),
            new RetailerException("Retailer {$this->userInput['retailer']} has not been found.")
        );

        return $retailer;
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

    protected function displayResults($results)
    {
        [$products, $pages] = $results;

        $this->table(
            array_keys($products[0]),
            $products
        );
        $this->info(json_encode($pages));
    }
}
