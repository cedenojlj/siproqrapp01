<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'warehouse_id',
        'order_type',
        'total_amount',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}