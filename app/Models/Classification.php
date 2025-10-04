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
        'precio_unidad',
        'precio_peso',      
        'unit_type',
        'name',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}