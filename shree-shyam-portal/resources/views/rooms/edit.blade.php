@extends('layouts.app')

@section('title', 'Edit Room')
@section('header_title', 'Modify Room Details')
@section('header_subtitle', 'Edit registration properties for Room ' . $room->room_number)

@section('content')
<div class="glass-panel" style="max-width: 600px; margin: 0 auto;">
    <div class="section-header">
        <h2><i class="fa-solid fa-edit"></i> Room Properties</h2>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to Rooms
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div class="alert-content">
                <ul style="list-style: none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="room-edit-form" action="{{ route('rooms.update', $room->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="form-group">
                <label for="room_number">Room Number <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="room_number" id="room_number" class="form-control" value="{{ old('room_number', $room->room_number) }}" required>
            </div>

            <div class="form-group">
                <label for="room_type">Room Type <span style="color: var(--danger-color);">*</span></label>
                <select name="room_type" id="room_type" class="form-control" required>
                    <option value="Single AC" {{ old('room_type', $room->room_type) === 'Single AC' ? 'selected' : '' }}>Single AC</option>
                    <option value="Single Non-AC" {{ old('room_type', $room->room_type) === 'Single Non-AC' ? 'selected' : '' }}>Single Non-AC</option>
                    <option value="Double AC" {{ old('room_type', $room->room_type) === 'Double AC' ? 'selected' : '' }}>Double AC</option>
                    <option value="Double Non-AC" {{ old('room_type', $room->room_type) === 'Double Non-AC' ? 'selected' : '' }}>Double Non-AC</option>
                    <option value="Deluxe Suite" {{ old('room_type', $room->room_type) === 'Deluxe Suite' ? 'selected' : '' }}>Deluxe Suite</option>
                    <option value="Super Deluxe" {{ old('room_type', $room->room_type) === 'Super Deluxe' ? 'selected' : '' }}>Super Deluxe</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price_per_night">Price Per Night (INR) <span style="color: var(--danger-color);">*</span></label>
                <input type="number" name="price_per_night" id="price_per_night" class="form-control" value="{{ old('price_per_night', $room->price_per_night) }}" min="0" required>
            </div>

            <div class="form-group">
                <label for="status">Current Status <span style="color: var(--danger-color);">*</span></label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Available" {{ old('status', $room->status) === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Occupied" {{ old('status', $room->status) === 'Occupied' ? 'selected' : '' }} {{ $room->status === 'Occupied' ? 'selected' : 'disabled' }}>Occupied (Managed via Check-in/out)</option>
                    <option value="Cleaning" {{ old('status', $room->status) === 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                    <option value="Maintenance" {{ old('status', $room->status) === 'Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                </select>
                @if($room->status === 'Occupied')
                    <small class="form-help" style="color: var(--danger-color);">Room is currently occupied by a guest. Release it by completing check-out.</small>
                @endif
            </div>

            <div class="actions-group" style="margin-top: 1rem; justify-content: flex-end;">
                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $("#room-edit-form").validate({
        rules: {
            room_number: {
                required: true,
                minlength: 1,
                maxlength: 20
            },
            room_type: {
                required: true
            },
            price_per_night: {
                required: true,
                number: true,
                min: 0
            },
            status: {
                required: true
            }
        },
        messages: {
            room_number: {
                required: "Please enter room number.",
                maxlength: "Room number cannot exceed 20 characters."
            },
            room_type: {
                required: "Please select a room type."
            },
            price_per_night: {
                required: "Please specify room price.",
                number: "Please enter a valid price amount.",
                min: "Price cannot be negative."
            },
            status: {
                required: "Please select room status."
            }
        }
    });
});
</script>
@endsection
