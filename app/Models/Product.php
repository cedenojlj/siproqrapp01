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

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function petitionProducts()
    {
        return $this->hasMany(PetitionProduct::class);        
    }


    /* public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    } */

        
     //un producto puede estar en varios almacenes

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class);
    }


   //productwarehouse
    public function productWarehouses()
    {
        return $this->hasMany(ProductWarehouse::class);
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

    //stock por producto    public function getTotalStockAttribute()
    
    public function getTotalStockAttribute()
    {
        return $this->productWarehouses()->sum('stock');
    }   
}
