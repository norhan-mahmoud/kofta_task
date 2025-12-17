<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    protected $fillable = [
        'item_id',
        'batch_id',
        'amount',
        'action_type',
        'reference_type',
        'reference_id',
        'created_at',
    ];
    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    
}
