<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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
        if ($this->retailer->name === 'Best Buy') {
            $results = Http::get('http://foo.test')->json();

            $this->update([
                'in_stock' => $results['available'],
                'price' => $results['price']
            ]);
        }
    }
}
