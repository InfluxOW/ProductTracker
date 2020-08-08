<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class AddWelcomeMessageToOutput
{
    public function handle(CommandStarting $event)
    {
        if (Str::startsWith($event->command, 'tracker')) {
            $event->output->writeLn("<options=bold;bg=cyan> Welcome to Product Tracker! </>");
            $event->output->write(PHP_EOL);
        }
    }
}
