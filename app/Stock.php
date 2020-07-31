<?php

namespace App;

use Facades\App\Clients\ClientFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['price', 'url', 'sku', 'in_stock'];
    protected $table = 'stock';
    protected $casts = [
        'in_stock' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function track()
    {
        $status = $this->retailer->client()
            ->checkAvailability($this);

        $this->update([
            'in_stock' => $status->available,
            'price' => $status->price
        ]);
    }
}
