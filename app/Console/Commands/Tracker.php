<?php

namespace App\Console\Commands;

use App\Exceptions\RetailerException;
use App\Retailer;
use Illuminate\Console\Command;

abstract class Tracker extends Command
{
    public function getRetailer($input)
    {
        $retailer = Retailer::all()
            ->filter(function($retailer) use ($input) {
                return toLowercaseWord($input) === toLowercaseWord($retailer->name);
            })->first();

        throw_if(
            is_null($retailer),
            new RetailerException("Retailer {$input} has not been found.")
        );

        return $retailer;
    }
}
