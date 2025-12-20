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

    public function batch()
    {
        return $this->morphOne(Batch::class, 'source');
    }

}
