<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = ['price', 'in_stock'];
    protected $table = 'product_history';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
