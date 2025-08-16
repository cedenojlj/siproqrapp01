<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petition extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'status',
        'total',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petitionProducts()
    {
        return $this->hasMany(PetitionProduct::class);
    }
}