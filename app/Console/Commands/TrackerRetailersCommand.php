<?php

namespace App\Console\Commands;

class TrackerRetailersCommand extends Tracker
{
    protected $signature = 'tracker:retailers';
    protected $description = 'Show all available retailers';

    public function handle()
    {
        $retailers = collect(
            getFilesInfo(app_path('Clients/Implementations'))
        )
            ->map->getFilenameWithoutExtension();

        $this->line("Currently available retailers are...");
        $this->info(json_encode($retailers));
    }
}
