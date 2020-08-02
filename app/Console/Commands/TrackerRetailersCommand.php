<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TrackerRetailersCommand extends Command
{
    protected $signature = 'tracker:retailers';
    protected $description = 'Show all available retailers';

    public function handle()
    {
        $retailers = collect(
            $this->getFilesInfo(app_path('Clients/Implementations'))
        )
            ->map->getFilenameWithoutExtension();

        $this->line("Currently available retailers are...");
        $this->info(json_encode($retailers));
    }
}
