<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id','item_id','quantity','unit_price'];

    public $timestamps = false; 

    public function item() {
        return $this->belongsTo(Item::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function batches()
    {
        return $this->belongsToMany(
            Batch::class,        // الموديل اللي مرتبط به
            'order_item_batches', // اسم جدول الربط
            'order_item_id',      // foreign key في جدول الربط اللي بيرتبط بـ OrderItem
            'batch_id'            // foreign key في جدول الربط اللي بيرتبط بـ Batch
        );
    }

}
