<?php

namespace App\Console\Commands;

class TrackerRetailersCommand extends Tracker
{
    protected $signature = 'tracker:retailers';
    protected $description = 'Show all available retailers';

    public function isHidden()
    {
        return false;
    }

    public function handle()
    {
        $this->line("Currently available retailers are...");
        $this->info(json_encode($this->retailers()));
    }
}
