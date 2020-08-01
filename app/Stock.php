<?php

namespace App;

use App\Events\NowInStock;
use App\UseCases\TrackStock;
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
        TrackStock::dispatch($this);
    }
}
