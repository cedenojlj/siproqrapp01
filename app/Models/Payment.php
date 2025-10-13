<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'notas',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function applications()
    {
        return $this->hasMany(PaymentApplication::class);
    }
}
