<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classification extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'code',
        'description',
        'size',
        'unit_type',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}