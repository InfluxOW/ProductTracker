<?php

namespace App;

use App\UseCases\TrackProduct;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'url', 'sku', 'in_stock'];
    protected $casts = [
        'in_stock' => 'boolean'
    ];

    public function history()
    {
        return $this->hasMany(History::class);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function track()
    {
        TrackProduct::dispatch($this);
    }
}
