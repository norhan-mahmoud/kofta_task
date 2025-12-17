<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $fillable = [
        'supplier_name',
        'status',
        'received_by',
        'received_at',
        'total_cost',
    ];
    protected $table = 'supplies';

    public $timestamps = false;

    public function batches()
    {
        return $this->hasMany(Batch::class, 'source_id')
            ->where('source_type', 'supply');
    }

}
