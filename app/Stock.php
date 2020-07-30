<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['price', 'url', 'sku', 'in_stock'];
    protected $table = 'stock';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
