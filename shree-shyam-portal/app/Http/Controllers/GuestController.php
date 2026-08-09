<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Guest;
use App\Models\Room;
use App\Models\StayRecord;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $guests = $query->orderBy('name')->paginate(10);
        return view('guests.index', compact('guests'));
    }

    public function show(Guest $guest)
    {
        $guest->load(['stayRecords' => function($q) {
            $q->with('room')->orderBy('check_in', 'desc');
        }]);

        return view('guests.show', compact('guest'));
    }

    public function showCheckinForm(Request $request)
    {
        $entryType = $request->query('entry_type', 'checkin');
        $rooms = Room::orderBy('room_number')->get();
        return view('guests.checkin', compact('rooms', 'entryType'));
    }

    public function lookup(Request $request)
    {
        $idType = $request->id_type;
        $idNumber = $request->id_number;

        if (!$idType || !$idNumber) {
            return response()->json(['found' => false]);
        }

        $cleanId = str_replace([' ', '-'], '', $idNumber);

        $guest = Guest::where('id_type', $idType)
            ->where(function($query) use ($idNumber, $cleanId) {
                $query->where('id_number', $idNumber)
                      ->orWhereRaw("REPLACE(REPLACE(id_number, ' ', ''), '-', '') = ?", [$cleanId]);
            })
            ->first();

        if ($guest) {
            $paths = $guest->id_proof_path ?? [];
            if (!is_array($paths)) {
                $paths = $paths ? [$paths] : [];
            }
            $formattedPaths = array_map(function($path) {
                return asset('storage/' . $path);
            }, $paths);

            return response()->json([
                'found' => true,
                'guest' => [
                    'name' => $guest->name,
                    'phone' => $guest->phone,
                    'email' => $guest->email,
                    'address' => $guest->address,
                    'city' => $guest->city,
                    'state' => $guest->state,
                    'id_proof_paths' => $formattedPaths,
                ]
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function checkin(Request $request)
    {
        // Check if guest already exists
        $guestExists = false;
        if ($request->id_type && $request->id_number) {
            $cleanId = str_replace([' ', '-'], '', $request->id_number);
            $guestExists = Guest::where('id_type', $request->id_type)
                ->where(function($query) use ($request, $cleanId) {
                    $query->where('id_number', $request->id_number)
                          ->orWhereRaw("REPLACE(REPLACE(id_number, ' ', ''), '-', '') = ?", [$cleanId]);
                })
                ->exists();
        }

        $rules = [
            // Guest Details
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'id_type' => 'required|string|in:Aadhar Card,PAN Card,Passport,Voter ID,Driving License',
            'id_number' => 'required|string|max:50',
            'id_proof' => $guestExists ? 'nullable|array' : 'required|array|min:1',
            'id_proof.*' => 'file|mimes:jpeg,png,jpg,pdf|max:4096',

            // Stay Details
            'entry_type' => 'required|string|in:checkin,booking',
            'room_id' => 'required|exists:rooms,id',
            'price_per_night' => 'required|numeric|min:0',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|in:Cash,UPI,Card',
            'purpose' => 'nullable|string|max:255',
        ];

        if ($request->entry_type === 'booking') {
            $rules['check_in'] = 'required|date|after_or_equal:today';
            $rules['expected_check_out'] = 'required|date|after:check_in';
        } else {
            $rules['check_in'] = 'required|date|before_or_equal:now';
            $rules['expected_check_out'] = 'nullable|date|after:check_in';
        }

        $request->validate($rules);

        // Check for date overlaps
        $checkIn = $request->check_in;
        $checkOut = $request->expected_check_out ?? date('Y-m-d H:i:s', strtotime($checkIn . ' +1 day'));

        $overlap = StayRecord::where('room_id', $request->room_id)
            ->whereIn('status', ['Active', 'Booked'])
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                      ->whereRaw('COALESCE(expected_check_out, NOW()) > ?', [$checkIn]);
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->withInput()->with('error', 'This room is already reserved or occupied during the selected dates.');
        }

        // Find or create guest by ID Type and ID Number
        $guest = Guest::where('id_type', $request->id_type)->where('id_number', $request->id_number)->first();
        if (!$guest) {
            $guest = Guest::where('phone', $request->phone)->first();
        }

        // Standardize current paths to array
        $idProofPaths = $guest ? ($guest->id_proof_path ?? []) : [];
        if (!is_array($idProofPaths)) {
            $idProofPaths = $idProofPaths ? [$idProofPaths] : [];
        }

        // Append new files if uploaded
        if ($request->hasFile('id_proof')) {
            foreach ($request->file('id_proof') as $file) {
                $idProofPaths[] = $file->store('id_proofs', 'public');
            }
        }

        if (!$guest) {
            $guest = Guest::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'id_proof_path' => $idProofPaths,
            ]);
        } else {
            // Update details of existing guest
            $guest->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'id_proof_path' => $idProofPaths,
            ]);
        }

        // Get Room details
        $room = Room::findOrFail($request->room_id);

        if ($request->entry_type === 'checkin') {
            if ($room->status !== 'Available') {
                return redirect()->back()->withInput()->with('error', 'Room is currently not available for instant check-in.');
            }

            StayRecord::create([
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in' => $request->check_in,
                'expected_check_out' => $request->expected_check_out,
                'adults' => $request->adults,
                'children' => $request->children,
                'price_per_night' => $request->price_per_night,
                'advance_payment' => $request->advance_payment ?? 0,
                'payment_mode' => $request->payment_mode,
                'status' => 'Active',
                'purpose' => $request->purpose,
            ]);

            // Mark Room as Occupied
            $room->update(['status' => 'Occupied']);

            return redirect()->route('dashboard')->with('success', "Guest {$guest->name} checked into Room {$room->room_number} successfully.");
        } else {
            // Create Stay Record as Booked (Room status remains Available)
            StayRecord::create([
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in' => $request->check_in,
                'expected_check_out' => $request->expected_check_out,
                'adults' => $request->adults,
                'children' => $request->children,
                'price_per_night' => $request->price_per_night,
                'advance_payment' => $request->advance_payment ?? 0,
                'payment_mode' => $request->payment_mode,
                'status' => 'Booked',
                'purpose' => $request->purpose,
            ]);

            return redirect()->route('bookings.index')->with('success', "Advance booking for Room {$room->room_number} registered successfully.");
        }
    }

    // List all bookings
    public function bookingsIndex(Request $request)
    {
        $statusFilter = $request->input('status', 'ActiveBooked'); // Default to showing both Active and Booked stays

        if ($statusFilter === 'ActiveBooked') {
            $activeStays = StayRecord::with(['guest', 'room'])
                ->where('status', 'Active')
                ->orderBy('check_in', 'asc')
                ->get();

            $upcomingReservations = StayRecord::with(['guest', 'room'])
                ->where('status', 'Booked')
                ->orderBy('check_in', 'asc')
                ->get();

            return view('bookings.index', compact('activeStays', 'upcomingReservations', 'statusFilter'));
        }

        $query = StayRecord::with(['guest', 'room']);

        if ($statusFilter === 'All') {
            $query->whereIn('status', ['Booked', 'Active', 'Completed', 'Cancelled']);
        } else {
            $query->where('status', $statusFilter);
        }

        $bookings = $query->orderBy('check_in', 'desc')->paginate(15);
        return view('bookings.index', compact('bookings', 'statusFilter'));
    }

    // Convert Booked reservation to Checked-In active stay
    public function checkinBooking(StayRecord $stayRecord)
    {
        if ($stayRecord->status !== 'Booked') {
            return redirect()->back()->with('error', 'Only reserved bookings can be checked in.');
        }

        // Rule check: Cannot check in before the booked check-in day!
        if (now()->startOfDay()->lt($stayRecord->check_in->startOfDay())) {
            return redirect()->back()->with('error', 'Guest cannot check in before the booked date (' . $stayRecord->check_in->format('d M Y') . ').');
        }

        $room = $stayRecord->room;
        if ($room->status === 'Occupied') {
            return redirect()->back()->with('error', "Room {$room->room_number} is currently occupied by another guest. Complete their check-out first.");
        }

        // Activate stay and set room status to Occupied
        $stayRecord->update([
            'status' => 'Active',
            'check_in' => now(), // Update check-in to actual arrival time
        ]);

        $room->update(['status' => 'Occupied']);

        return redirect()->route('dashboard')->with('success', "Guest {$stayRecord->guest->name} has checked into Room {$room->room_number} successfully.");
    }

    // Cancel booking
    public function cancelBooking(StayRecord $stayRecord)
    {
        if ($stayRecord->status !== 'Booked') {
            return redirect()->back()->with('error', 'Only upcoming reservations can be cancelled.');
        }

        $stayRecord->update(['status' => 'Cancelled']);

        return redirect()->route('bookings.index')->with('success', 'Booking cancelled successfully.');
    }

    // Show Edit Booking Form
    public function editBookingForm(StayRecord $stayRecord)
    {
        $stayRecord->load(['guest', 'room']);
        $rooms = Room::orderBy('room_number')->get();
        return view('bookings.edit', compact('stayRecord', 'rooms'));
    }

    // Update Stay Record & Guest Profile details
    public function updateBooking(Request $request, StayRecord $stayRecord)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'room_id' => 'required|exists:rooms,id',
            'price_per_night' => 'required|numeric|min:0',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'advance_payment' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|in:Cash,UPI,Card',
            'purpose' => 'nullable|string|max:255',
        ];

        if ($stayRecord->status === 'Booked') {
            $rules['check_in'] = 'required|date';
            $rules['expected_check_out'] = 'required|date|after:check_in';
        } else {
            $rules['check_in'] = 'required|date';
            $rules['expected_check_out'] = 'nullable|date|after:check_in';
        }

        $request->validate($rules);

        // Overlap Validation excluding self
        $checkIn = $request->check_in;
        $checkOut = $request->expected_check_out ?? date('Y-m-d H:i:s', strtotime($checkIn . ' +1 day'));

        $overlap = StayRecord::where('room_id', $request->room_id)
            ->where('id', '!=', $stayRecord->id)
            ->whereIn('status', ['Active', 'Booked'])
            ->where(function($query) use ($checkIn, $checkOut) {
                $query->where('check_in', '<', $checkOut)
                      ->whereRaw('COALESCE(expected_check_out, NOW()) > ?', [$checkIn]);
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->withInput()->with('error', 'The selected room is reserved or occupied during those dates.');
        }

        // Handle Room status updates
        if ($stayRecord->room_id != $request->room_id) {
            if ($stayRecord->status === 'Active') {
                $stayRecord->room->update(['status' => 'Available']);
            }
            $newRoom = Room::findOrFail($request->room_id);
            if ($stayRecord->status === 'Active') {
                if ($newRoom->status !== 'Available') {
                    return redirect()->back()->withInput()->with('error', 'New room is not available for check-in.');
                }
                $newRoom->update(['status' => 'Occupied']);
            }
        }

        // Save Guest profile details
        $stayRecord->guest->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
        ]);

        // Update Stay record
        $stayRecord->update([
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'expected_check_out' => $request->expected_check_out,
            'price_per_night' => $request->price_per_night,
            'advance_payment' => $request->advance_payment ?? 0,
            'payment_mode' => $request->payment_mode,
            'adults' => $request->adults,
            'children' => $request->children,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Stay record details updated successfully.');
    }
}
