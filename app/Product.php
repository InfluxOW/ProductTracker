<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name'];

    public function inStock()
    {
        return $this->stock()->where('in_stock', true)->exists();
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function track()
    {
        $this->stock->each(function(Stock $stock) {
            $stock->track();
            $this->recordHistory($stock);
        });
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }

    public function recordHistory(Stock $stock)
    {
        $history = History::make([
            'price' => $stock->price,
            'in_stock' => $stock->in_stock,
        ]);

        $history->product()->associate($this);
        $history->stock()->associate($stock);
        $history->save();
    }
}
