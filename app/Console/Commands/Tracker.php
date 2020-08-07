<?php /** @noinspection PhpVoidFunctionResultUsedInspection */

namespace App\Console\Commands;

use App\Exceptions\RetailerException;
use App\Retailer;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Tracker extends Command
{
    protected $signature = 'tracker';

    public function isHidden()
    {
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->handle();
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
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

    public function retailers()
    {
        return array_map(function ($file) {
            return $file->getFilenameWithoutExtension();
        }, getFilesInfo(app_path('Clients/Implementations')));
    }

    public function productValidationRules()
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
