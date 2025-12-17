<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manufacturing extends Model
{
    protected $table = 'manufacturing';

    protected $fillable = [
        'out_item_id',
        'out_amount',
        'factory_date',
    ];

    

    public $timestamps = false;

    public function items() {
        return $this->hasMany(ItemInManufacturing::class);
    }

    public function batch() {
        return $this->hasOne(Batch::class,'source_id')->where('source_type','manufacturing');
    }


}
