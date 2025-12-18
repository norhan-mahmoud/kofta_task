<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
     protected $fillable = ['order_number','customer_name','status','total_price'];

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $lastId = self::max('id') ?? 0;
            $order->order_number = 'ORD-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    public function stocklogs()
    {
        return $this->hasMany(StockLog::class,'reference_id')->where('reference_type','order');
    }
    


}
