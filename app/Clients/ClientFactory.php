<?php

namespace App\Clients;

use App\Exceptions\ClientException;
use App\Retailer;
use App\Stock;
use Illuminate\Support\Str;

class ClientFactory
{
    public function make(Retailer $retailer): Client
    {
        $class = "App\\Clients\\Implementations\\" . Str::studly($retailer->name);

        throw_if(!class_exists($class), new ClientException("Client not found for {$retailer->name}."));

        return (new $class);
    }
}
