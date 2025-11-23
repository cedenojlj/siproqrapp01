<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetitionClassification extends Model
{
    //
    protected $fillable = [
        'petition_id',
        'classification_id',
        'quantity',
    ];

    public function petition()
    {
        return $this->belongsTo(Petition::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }
}
