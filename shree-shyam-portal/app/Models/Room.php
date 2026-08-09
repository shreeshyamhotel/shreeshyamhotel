<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'room_type',
        'price_per_night',
        'status',
    ];

    public function stayRecords()
    {
        return $this->hasMany(StayRecord::class);
    }

    public function activeStay()
    {
        return $this->hasOne(StayRecord::class)->where('status', 'Active');
    }

    public function isAvailable()
    {
        return $this->status === 'Available';
    }
}
