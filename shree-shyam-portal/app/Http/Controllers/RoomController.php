<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number',
            'room_type' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|string|in:Available,Occupied,Cleaning,Maintenance',
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Room added successfully.');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'room_type' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|string|in:Available,Occupied,Cleaning,Maintenance',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->status === 'Occupied') {
            return redirect()->route('rooms.index')->with('error', 'Cannot delete an occupied room.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }

    public function availability(Room $room)
    {
        $stays = \App\Models\StayRecord::with('guest')
            ->where('room_id', $room->id)
            ->whereIn('status', ['Active', 'Booked'])
            ->orderBy('check_in', 'asc')
            ->get();

        $activeStay = $stays->where('status', 'Active')->first();
        
        // Check if there is an advance reservation active right now
        $now = now();
        $reservedStayNow = $stays->where('status', 'Booked')->filter(function($stay) use ($now) {
            $checkOut = $stay->expected_check_out ?? $now->copy()->addDay();
            return $stay->check_in <= $now && $checkOut >= $now;
        })->first();

        $timeline = [];
        foreach ($stays as $stay) {
            $timeline[] = [
                'guest_name' => $stay->guest->name,
                'status' => $stay->status === 'Active' ? 'Checked-In' : 'Reserved',
                'check_in' => $stay->check_in->format('d M Y h:i A'),
                'check_out' => $stay->expected_check_out ? $stay->expected_check_out->format('d M Y h:i A') : 'Indefinite',
            ];
        }

        // Determine effective display status
        $displayStatus = $room->status; // 'Available', 'Occupied', 'Cleaning', 'Maintenance'
        if ($displayStatus === 'Available' && $reservedStayNow) {
            $displayStatus = 'Reserved';
        }

        return response()->json([
            'room_number' => $room->room_number,
            'room_status' => $displayStatus,
            'is_occupied' => $room->status === 'Occupied',
            'is_reserved_now' => !is_null($reservedStayNow),
            'active_stay' => $activeStay ? [
                'guest_name' => $activeStay->guest->name,
                'check_out' => $activeStay->expected_check_out ? $activeStay->expected_check_out->format('d M Y h:i A') : 'Indefinite',
            ] : null,
            'reserved_stay_now' => $reservedStayNow ? [
                'guest_name' => $reservedStayNow->guest->name,
                'check_in' => $reservedStayNow->check_in->format('d M Y h:i A'),
                'check_out' => $reservedStayNow->expected_check_out ? $reservedStayNow->expected_check_out->format('d M Y h:i A') : 'Indefinite',
            ] : null,
            'timeline' => $timeline,
        ]);
    }
}
