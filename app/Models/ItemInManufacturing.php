<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemInManufacturing extends Model
{
    
    protected $table = 'item_in_manufacturing';
    public $timestamps = false;
    protected $fillable = [
        'manufacturing_id',
        'item_id',
        'amount',
    ];

    public function manufacturing() {
        return $this->belongsTo(Manufacturing::class);
    }
    public function item() {
        return $this->belongsTo(Item::class);
    }
}
