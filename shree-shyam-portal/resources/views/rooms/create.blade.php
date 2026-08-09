@extends('layouts.app')

@section('title', 'Add Room')
@section('header_title', 'Add New Room')
@section('header_subtitle', 'Register a new room in the hotel inventory')

@section('content')
<div class="glass-panel" style="max-width: 600px; margin: 0 auto;">
    <div class="section-header">
        <h2><i class="fa-solid fa-plus-circle"></i> Room Information</h2>
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

    <form id="room-create-form" action="{{ route('rooms.store') }}" method="POST">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="form-group">
                <label for="room_number">Room Number <span style="color: var(--danger-color);">*</span></label>
                <input type="text" name="room_number" id="room_number" class="form-control" placeholder="e.g. 101, 204, G-2" value="{{ old('room_number') }}" required>
            </div>

            <div class="form-group">
                <label for="room_type">Room Type <span style="color: var(--danger-color);">*</span></label>
                <select name="room_type" id="room_type" class="form-control" required>
                    <option value="" disabled selected>Select Room Type</option>
                    <option value="Single AC" {{ old('room_type') === 'Single AC' ? 'selected' : '' }}>Single AC</option>
                    <option value="Single Non-AC" {{ old('room_type') === 'Single Non-AC' ? 'selected' : '' }}>Single Non-AC</option>
                    <option value="Double AC" {{ old('room_type') === 'Double AC' ? 'selected' : '' }}>Double AC</option>
                    <option value="Double Non-AC" {{ old('room_type') === 'Double Non-AC' ? 'selected' : '' }}>Double Non-AC</option>
                    <option value="Deluxe Suite" {{ old('room_type') === 'Deluxe Suite' ? 'selected' : '' }}>Deluxe Suite</option>
                    <option value="Super Deluxe" {{ old('room_type') === 'Super Deluxe' ? 'selected' : '' }}>Super Deluxe</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price_per_night">Price Per Night (INR) <span style="color: var(--danger-color);">*</span></label>
                <input type="number" name="price_per_night" id="price_per_night" class="form-control" placeholder="e.g. 1500" value="{{ old('price_per_night') }}" min="0" required>
            </div>

            <div class="form-group">
                <label for="status">Initial Status <span style="color: var(--danger-color);">*</span></label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Available" {{ old('status', 'Available') === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Cleaning" {{ old('status') === 'Cleaning' ? 'selected' : '' }}>Cleaning / Maintenance</option>
                    <option value="Maintenance" {{ old('status') === 'Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                </select>
            </div>

            <div class="actions-group" style="margin-top: 1rem; justify-content: flex-end;">
                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Register Room
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $("#room-create-form").validate({
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
