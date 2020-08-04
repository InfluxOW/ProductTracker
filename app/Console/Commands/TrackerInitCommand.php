<?php

namespace App\Console\Commands;

use App\Product;

class TrackerInitCommand extends Tracker
{
    protected $signature = 'tracker:init';
    protected $description = 'Initialize tracking on every project';

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
        $data = Product::get($this->keys());

        $this->table(
            $this->keys(),
            $data
        );
    }

    protected function keys()
    {
        return ['name', 'price', 'sku', 'url', 'in_stock'];
    }
}
