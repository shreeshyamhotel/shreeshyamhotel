<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class StayRecord extends Model
{
    protected $fillable = [
        'guest_id',
        'room_id',
        'check_in',
        'expected_check_out',
        'actual_check_out',
        'adults',
        'children',
        'price_per_night',
        'advance_payment',
        'payment_mode',
        'discount',
        'tax_amount',
        'status',
        'purpose',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'expected_check_out' => 'datetime',
        'actual_check_out' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function extraCharges()
    {
        return $this->hasMany(ExtraCharge::class);
    }

    // Accessors for Invoice calculations
    public function getNightsAttribute()
    {
        $checkIn = Carbon::parse($this->check_in);
        $checkOut = $this->actual_check_out ? Carbon::parse($this->actual_check_out) : now();

        $startDate = $checkIn->copy()->startOfDay();
        $endDate = $checkOut->copy()->startOfDay();
        $diff = (int) $startDate->diffInDays($endDate);

        if ($diff === 0) {
            return 1;
        }

        // Standard 11:00 AM Checkout limit
        // If checkout is after 11:00 AM, charge 1 extra day/night
        $checkoutHour = (int) $checkOut->format('H');
        $checkoutMinute = (int) $checkOut->format('i');

        if ($checkoutHour > 11 || ($checkoutHour === 11 && $checkoutMinute > 0)) {
            return $diff + 1;
        }

        return $diff;
    }

    public function getRoomChargesAttribute()
    {
        return $this->nights * $this->price_per_night;
    }

    public function getExtraChargesTotalAttribute()
    {
        return $this->extraCharges()->sum('amount');
    }

    public function getGrossTotalAttribute()
    {
        return $this->room_charges + $this->extra_charges_total;
    }

    public function getNetTotalAttribute()
    {
        return $this->gross_total - $this->discount + $this->tax_amount;
    }

    public function getBalanceDueAttribute()
    {
        return $this->net_total - $this->advance_payment;
    }
}
