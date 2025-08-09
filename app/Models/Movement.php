<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'type',
        'order_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}