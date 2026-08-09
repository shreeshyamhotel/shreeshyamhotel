<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraCharge extends Model
{
    protected $fillable = [
        'stay_record_id',
        'amount',
        'description',
        'bill_number',
    ];

    public function stayRecord()
    {
        return $this->belongsTo(StayRecord::class);
    }
}
