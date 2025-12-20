<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name','type','for_sale'];

    public function batches() {
        return $this->hasMany(Batch::class);
    }

    

    public function stockLogs() {
        return $this->hasMany(StockLog::class);
    }

    public function scopeAvailable($query)
    {
        return $query->whereHas('batches', function($q){
            $q->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '>', now());
        });
    }

    public function manufacturingsOut() {
        return $this->hasMany(Manufacturing::class,'out_item_id');
    }

}
