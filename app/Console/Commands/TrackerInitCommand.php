<?php

namespace App\Console\Commands;

use App\Product;

class TrackerInitCommand extends Tracker
{
    protected $signature = 'tracker:init';
    protected $description = 'Initialize tracking on every product';

    public function handle()
    {
        try {
            $this->output->progressStart(Product::count());

            Product::each(function($product) {
                $product->track();
                $this->output->progressAdvance();
            });

            $this->output->progressFinish();

            $this->showResults();
        } catch (\Exception $e) {
            $this->line(PHP_EOL);
            $this->error($e->getMessage());
        }
    }

    protected function showResults()
    {
        $this->table(
            $this->keys(),
            Product::get($this->keys())
        );
    }

    protected function keys()
    {
        return ['name', 'price', 'sku', 'url', 'in_stock'];
    }
}
