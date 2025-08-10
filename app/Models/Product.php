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
        'sku',
        'type',
        'size',
        'GN',
        'GW',
        'Box',
        'invoice_number',
        'classification_id',        
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
            'sku' => $this->sku,
            'type' => $this->type,
            'size' => $this->size,
            'GN' => $this->GN,
            'GW' => $this->GW,
            'Box' => $this->Box,
            'invoice_number' => $this->invoice_number,
            'classification_id' => $this->classification_id,            
        ]);

        return QrCode::size(200)->generate($data);
    }
}
