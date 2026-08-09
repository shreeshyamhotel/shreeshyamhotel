<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\StayRecord;
use App\Models\ExtraCharge;
use App\Models\Room;

class BillingController extends Controller
{
    public function showCheckoutForm(StayRecord $stayRecord)
    {
        if ($stayRecord->status !== 'Active') {
            return redirect()->route('checkout.invoice', $stayRecord);
        }

        $stayRecord->load(['guest', 'room', 'extraCharges']);
        return view('billing.checkout', compact('stayRecord'));
    }

    public function addExtraCharge(Request $request, StayRecord $stayRecord)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'bill_number' => 'nullable|string|max:50',
        ]);

        $stayRecord->extraCharges()->create([
            'amount' => $request->amount,
            'description' => $request->description,
            'bill_number' => $request->bill_number,
        ]);

        return redirect()->route('checkout.form', $stayRecord)->with('success', 'Extra charge added successfully.');
    }

    public function checkout(Request $request, StayRecord $stayRecord)
    {
        if ($stayRecord->status !== 'Active') {
            return redirect()->route('checkout.invoice', $stayRecord);
        }

        $request->validate([
            'discount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'required|string|in:Cash,UPI,Card',
            'room_status_after' => 'required|string|in:Available,Cleaning,Maintenance',
        ]);

        // Finalize checkout details
        $stayRecord->update([
            'actual_check_out' => now(),
            'discount' => $request->discount ?? 0,
            'tax_amount' => $request->tax_amount ?? 0,
            'payment_mode' => $request->payment_mode,
            'status' => 'Completed',
        ]);

        // Release Room and update its status
        $room = $stayRecord->room;
        $room->update(['status' => $request->room_status_after]);

        return redirect()->route('checkout.invoice', $stayRecord)->with('success', 'Guest checked out successfully. Invoice generated.');
    }

    public function invoice(StayRecord $stayRecord)
    {
        $stayRecord->load(['guest', 'room', 'extraCharges']);
        return view('billing.invoice', compact('stayRecord'));
    }

    public function monthlyReport(Request $request)
    {
        // Default to current year & month
        $selectedMonth = $request->input('month', date('Y-m'));
        
        try {
            $parsedDate = Carbon::parse($selectedMonth . '-01');
        } catch (\Exception $e) {
            $parsedDate = Carbon::now();
            $selectedMonth = $parsedDate->format('Y-m');
        }

        $year = $parsedDate->year;
        $month = $parsedDate->month;

        $monthStart = $parsedDate->copy()->startOfMonth()->startOfDay();
        $monthEnd = $parsedDate->copy()->endOfMonth()->endOfDay();

        // Fetch all completed stay records that overlap with the selected month
        $records = StayRecord::with(['guest', 'room', 'extraCharges'])
            ->where('status', 'Completed')
            ->where('check_in', '<=', $monthEnd)
            ->where('actual_check_out', '>=', $monthStart)
            ->orderBy('check_in', 'asc')
            ->get();

        // Calculate aggregates
        $totalStays = $records->count();
        
        $totalAdvance = 0;
        $totalRoomCharges = 0;
        $totalExtraCharges = 0;
        $totalDiscounts = 0;
        $totalTaxes = 0;
        $totalCollection = 0;

        foreach ($records as $record) {
            $totalNights = $record->nights;
            $checkInDate = $record->check_in->copy()->startOfDay();
            
            // Count how many nights of this stay fell in the selected month
            $nightsInMonth = 0;
            for ($i = 0; $i < $totalNights; $i++) {
                $nightDate = $checkInDate->copy()->addDays($i);
                if ($nightDate->year === $year && $nightDate->month === $month) {
                    $nightsInMonth++;
                }
            }

            $ratio = $totalNights > 0 ? ($nightsInMonth / $totalNights) : 0;

            // Apportioned values for this month
            $record->nights_in_month = $nightsInMonth;
            $record->room_charges_in_month = $nightsInMonth * $record->price_per_night;

            // Assign extra charges to the specific month they were incurred
            $record->extra_charges_in_month = $record->extraCharges->filter(function($charge) use ($year, $month) {
                return $charge->created_at->year === $year && $charge->created_at->month === $month;
            })->sum('amount');

            // Apportion discount & tax
            $record->discount_in_month = $record->discount * $ratio;
            $record->tax_in_month = $record->tax_amount * $ratio;

            // Net Collection in Month
            $record->net_collection_in_month = $record->room_charges_in_month + $record->extra_charges_in_month - $record->discount_in_month + $record->tax_in_month;

            // Check-in Advance: Count only if checked in during this month
            $record->advance_in_month = ($record->check_in->year === $year && $record->check_in->month === $month)
                ? $record->advance_payment
                : 0;

            // Settlement: Count only if checkout happened during this month
            $record->settlement_in_month = 0;
            if ($record->actual_check_out && $record->actual_check_out->year === $year && $record->actual_check_out->month === $month) {
                $record->settlement_in_month = $record->net_total - $record->advance_payment;
            }

            // Sum aggregates
            $totalAdvance += $record->advance_in_month;
            $totalRoomCharges += $record->room_charges_in_month;
            $totalExtraCharges += $record->extra_charges_in_month;
            $totalDiscounts += $record->discount_in_month;
            $totalTaxes += $record->tax_in_month;
            $totalCollection += $record->net_collection_in_month;
        }

        return view('reports.monthly', compact(
            'records',
            'selectedMonth',
            'totalStays',
            'totalAdvance',
            'totalRoomCharges',
            'totalExtraCharges',
            'totalDiscounts',
            'totalTaxes',
            'totalCollection'
        ));
    }
}
