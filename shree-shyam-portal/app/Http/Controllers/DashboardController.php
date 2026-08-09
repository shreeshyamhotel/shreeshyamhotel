<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Room;
use App\Models\Guest;
use App\Models\StayRecord;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'Available')->count(),
            'occupied_rooms' => Room::where('status', 'Occupied')->count(),
            'maintenance_rooms' => Room::whereIn('status', ['Maintenance', 'Cleaning'])->count(),
            'active_guests' => StayRecord::where('status', 'Active')->count(),
            'upcoming_bookings' => StayRecord::where('status', 'Booked')->count(),
        ];

        $activeStays = StayRecord::with(['room', 'guest'])
            ->where('status', 'Active')
            ->orderBy('check_in', 'desc')
            ->get();

        $recentRecords = StayRecord::with(['room', 'guest'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'activeStays', 'recentRecords'));
    }
}
