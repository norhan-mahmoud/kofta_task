<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{

    protected $table = 'batches';

    protected $fillable = [
        'item_id',
        'source_type',
        'source_id',
        'produced_at',
        'expired_date',
        'initial_quantity',
        'remaining_quantity',
    ];

    protected $casts = [
        'produced_at' => 'datetime',
        'expired_date' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class, 'source_id')->where('source_type', 'supply');
    }

    public function manufacturing()
    {
        return $this->belongsTo(Manufacturing::class, 'source_id')
            ->where('source_type', 'manufacturing');
    }


    
}
