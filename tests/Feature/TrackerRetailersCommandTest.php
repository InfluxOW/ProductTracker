<?php

namespace Tests\Feature;

use Facades\App\Console\Commands\Tracker;
use Tests\TestCase;

class TrackerRetailersCommandTest extends TestCase
{
    /** @test */
    public function it_shows_list_of_all_available_retailers()
    {
        $retailers = Tracker::retailers();

        $this->artisan('tracker:retailers')
            ->expectsOutput(json_encode($retailers));
    }
}
