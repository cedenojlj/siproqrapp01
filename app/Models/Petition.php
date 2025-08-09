<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petition extends Model
{
    protected $fillable = [
        'customer_id',
        'total_amount',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function petitionProducts()
    {
        return $this->hasMany(PetitionProduct::class);
    }
}