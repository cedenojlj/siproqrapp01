<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Warehouse extends Model
{
   use HasFactory;
   
    protected $fillable = [
        'name',
        'location',
    ];

    public function productWarehouses()
    {
        return $this->hasMany(ProductWarehouse::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}