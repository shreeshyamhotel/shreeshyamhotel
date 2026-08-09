<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'id_type',
        'id_number',
        'id_proof_path',
    ];

    protected $casts = [
        'id_proof_path' => 'array',
    ];

    public function stayRecords()
    {
        return $this->hasMany(StayRecord::class);
    }

    public function activeStay()
    {
        return $this->hasOne(StayRecord::class)->where('status', 'Active');
    }
}
