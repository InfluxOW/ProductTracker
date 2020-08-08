<?php

namespace App\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Str;

class AddThankYouMessageToOutput
{
    public function handle(CommandFinished $event)
    {
        if (Str::startsWith($event->command, 'tracker')) {
            $event->output->write(PHP_EOL);
            $event->output->writeLn("<options=bold;bg=cyan> Thank you for using Product Tracker!</>");
        }
    }
}
