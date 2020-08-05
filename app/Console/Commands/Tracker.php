<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

namespace App\Console\Commands;

use App\Exceptions\RetailerException;
use App\Retailer;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Tracker extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            return (int) $this->handle();
        } catch (\Exception $e) {
            return (int) $this->error($e->getMessage());
        }
    }

    public function getRetailer($input)
    {
        $retailer = Retailer::all()
            ->filter(function($retailer) use ($input) {
                return toLowercaseWord($input) === toLowercaseWord($retailer->name);
            })
            ->first();

        throw_if(
            is_null($retailer),
            new RetailerException("Retailer {$input} has not been found.")
        );

        return $retailer;
    }

    protected function retailers()
    {
        return array_map(function ($file) {
            return $file->getFilenameWithoutExtension();
        }, getFilesInfo(app_path('Clients/Implementations')));
    }


    protected function productValidationRules()
    {
        return [
            'name' => ['required', 'string', 'min:3'],
            'sku' => ['required', 'integer', 'min:1'],
            'url' => ['nullable', 'url'],
            'price' => ['nullable', 'integer', 'min:1'],
            'in_stock' => ['nullable', 'boolean']
        ];
    }
}
