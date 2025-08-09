<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'stock',
        'classification_id',
        'warehouse_id',
    ];

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function generateQrCode()
    {
        $data = json_encode([
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'classification_id' => $this->classification_id,
            'warehouse_id' => $this->warehouse_id,
        ]);

        return QrCode::size(200)->generate($data);
    }
}
