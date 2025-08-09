<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetitionProduct extends Model
{
    protected $fillable = [
        'petition_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function petition()
    {
        return $this->belongsTo(Petition::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}