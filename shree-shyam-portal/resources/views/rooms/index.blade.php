@extends('layouts.app')

@section('title', 'Room Management')
@section('header_title', 'Rooms Directory')
@section('header_subtitle', 'Manage rooms list, rates, and occupancy status')

@section('content')
<div class="glass-panel">
    <div class="section-header">
        <h2><i class="fa-solid fa-door-open"></i> All Rooms</h2>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Add New Room
        </a>
    </div>

    @if($rooms->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fa-solid fa-door-closed fa-3x mb-4"></i>
            <p>No rooms registered yet in the system.</p>
            <a href="{{ route('rooms.create') }}" class="btn btn-primary mt-4">
                <i class="fa-solid fa-plus"></i> Register First Room
            </a>
        </div>
    @else
        <div class="rooms-grid">
            @foreach($rooms as $room)
                <div class="glass-panel room-card">
                    <div class="room-card-header">
                        <div>
                            <span class="room-card-number">Room {{ $room->room_number }}</span>
                            <div class="room-card-type">{{ $room->room_type }}</div>
                        </div>
                        <div>
                            @if($room->status === 'Available')
                                <span class="badge badge-success">{{ $room->status }}</span>
                            @elseif($room->status === 'Occupied')
                                <span class="badge badge-danger">{{ $room->status }}</span>
                            @elseif($room->status === 'Cleaning')
                                <span class="badge badge-warning">{{ $room->status }}</span>
                            @else
                                <span class="badge badge-info">{{ $room->status }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div style="margin-top: 0.5rem;">
                        <span class="text-muted" style="font-size: 0.8rem; text-transform: uppercase;">Room Rate</span>
                        <div class="room-card-price">₹{{ number_format($room->price_per_night, 2) }} <span style="font-size: 0.8rem; font-weight: normal; color: var(--text-secondary);">/ night</span></div>
                    </div>

                    <div class="room-card-footer">
                        <div class="actions-group">
                            <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-secondary btn-sm" title="Edit Room Details">
                                <i class="fa-solid fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this room?')" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete Room" {{ $room->status === 'Occupied' ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
