<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'credit_balance',
    ];

    public function petitions()
    {
        return $this->hasMany(Petition::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}