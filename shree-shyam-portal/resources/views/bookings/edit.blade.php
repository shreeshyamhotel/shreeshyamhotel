@extends('layouts.app')

@section('title', 'Edit Stay / Booking')
@section('header_title', 'Edit Stay / Booking details')
@section('header_subtitle', 'Modify guest information, check-in/out dates, and rent settings')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Back Button -->
    <div>
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to list
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-xmark"></i>
            <div class="alert-content">{{ session('error') }}</div>
        </div>
    @endif

    <form id="edit-booking-form" action="{{ route('bookings.update', $stayRecord->id) }}" method="POST">
        @csrf
        
        <div class="checkout-grid">
            <!-- LEFT PANEL: GUEST DETAILS -->
            <div class="glass-panel" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #34d399; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-user-pen"></i> Guest Personal Details
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="name">Guest Name <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $stayRecord->guest->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $stayRecord->guest->phone) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $stayRecord->guest->email) }}">
                </div>


                <div style="background-color: rgba(255,255,255,0.02); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                    <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Stored ID Type</small>
                    <div style="font-weight: 600; color: #fff; margin-bottom: 0.5rem;">{{ $stayRecord->guest->id_type }}: <code>{{ $stayRecord->guest->id_number }}</code></div>
                    
                    @if($stayRecord->guest->id_proof_path)
                        @if(is_array($stayRecord->guest->id_proof_path))
                            @foreach($stayRecord->guest->id_proof_path as $idx => $path)
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" style="color: #818cf8; text-decoration: underline; font-size: 0.85rem; font-weight: 600; display: block; margin-top: 0.25rem;">
                                    <i class="fa-solid fa-file-invoice"></i> View Uploaded ID Document {{ $idx + 1 }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ asset('storage/' . $stayRecord->guest->id_proof_path) }}" target="_blank" style="color: #818cf8; text-decoration: underline; font-size: 0.85rem; font-weight: 600; display: block;">
                                <i class="fa-solid fa-file-invoice"></i> View Uploaded ID Document
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <!-- RIGHT PANEL: STAY DETAILS -->
            <div class="glass-panel" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #818cf8; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-hotel"></i> Stay & Billing Settings
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="room_id">Room Assigned <span style="color: var(--danger-color);">*</span></label>
                        <select name="room_id" id="room_id" class="form-control" required>
                            @foreach($rooms as $room)
                                @if($room->status === 'Available' || $room->id == $stayRecord->room_id)
                                    <option value="{{ $room->id }}" data-price="{{ $room->price_per_night }}" {{ old('room_id', $stayRecord->room_id) == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }} - {{ $room->room_type }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price_per_night">Room Rent Per Night (₹) <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="price_per_night" id="price_per_night" class="form-control" value="{{ old('price_per_night', $stayRecord->price_per_night) }}" min="0" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="check_in">Check-In Date & Time <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="check_in" id="check_in" class="form-control" value="{{ old('check_in', $stayRecord->check_in->format('Y-m-d H:i:s')) }}" required {{ $stayRecord->status === 'Active' ? 'readonly style=cursor:not-allowed;' : '' }}>
                    </div>
                    <div class="form-group">
                        <label for="expected_check_out">Expected Check-Out Date & Time <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="expected_check_out" id="expected_check_out" class="form-control" value="{{ old('expected_check_out', $stayRecord->expected_check_out ? $stayRecord->expected_check_out->format('Y-m-d H:i:s') : '') }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="advance_payment">Advance Payment (₹)</label>
                        <input type="number" name="advance_payment" id="advance_payment" class="form-control" value="{{ old('advance_payment', $stayRecord->advance_payment) }}" min="0">
                    </div>
                    <div class="form-group">
                        <label for="payment_mode">Advance Payment Mode</label>
                        <select name="payment_mode" id="payment_mode" class="form-control">
                            <option value="">None</option>
                            <option value="Cash" {{ old('payment_mode', $stayRecord->payment_mode) === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="UPI" {{ old('payment_mode', $stayRecord->payment_mode) === 'UPI' ? 'selected' : '' }}>UPI</option>
                            <option value="Card" {{ old('payment_mode', $stayRecord->payment_mode) === 'Card' ? 'selected' : '' }}>Card</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="adults">Adults <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="adults" id="adults" class="form-control" value="{{ old('adults', $stayRecord->adults) }}" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="children">Children <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="children" id="children" class="form-control" value="{{ old('children', $stayRecord->children) }}" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose of Stay / Remarks</label>
                    <input type="text" name="purpose" id="purpose" class="form-control" placeholder="e.g. Leisure, Business" value="{{ old('purpose', $stayRecord->purpose) }}">
                </div>

                <div style="margin-top: 1rem; display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-success" style="flex: 1;">
                        <i class="fa-solid fa-save"></i> Save Updates
                    </button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Configure date pickers
    var checkinPicker = flatpickr("#check_in", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S",
        altInput: true,
        altFormat: "d M Y h:i K",
        time_24hr: false
    });

    var checkoutPicker = flatpickr("#expected_check_out", {
        enableTime: true,
        dateFormat: "Y-m-d H:i:S",
        altInput: true,
        altFormat: "d M Y h:i K",
        time_24hr: false,
        minDate: "today"
    });

    // Populate rent rate when room is changed
    $("#room_id").change(function() {
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price') || 0;
        $("#price_per_night").val(price);
        $("#price_per_night").valid();
    });

    // jQuery validation
    jQuery.validator.addMethod("indianPhone", function(value, element) {
        return this.optional(element) || /^[6-9]\d{9}$/.test(value);
    }, "Please enter a valid 10-digit Indian mobile number.");

    jQuery.validator.addMethod("afterCheckin", function(value, element) {
        var start = $("#check_in").val();
        if(!start || !value) return true;
        return new Date(value) > new Date(start);
    }, "Check-out date/time must be after Check-in date/time.");

    $("#edit-booking-form").validate({
        rules: {
            name: {
                required: true,
                minlength: 3
            },
            phone: {
                required: true,
                indianPhone: true
            },
            email: {
                email: true
            },
            room_id: {
                required: true
            },
            price_per_night: {
                required: true,
                number: true,
                min: 0
            },
            check_in: {
                required: true
            },
            expected_check_out: {
                required: true,
                afterCheckin: true
            },
            adults: {
                required: true,
                integer: true,
                min: 1
            },
            children: {
                required: true,
                integer: true,
                min: 0
            },
            advance_payment: {
                number: true,
                min: 0
            },
            payment_mode: {
                required: function(element) {
                    var adv = parseFloat($("#advance_payment").val()) || 0;
                    return adv > 0;
                }
            }
        },
        messages: {
            name: {
                required: "Guest name is required."
            },
            phone: {
                required: "Phone number is required."
            },
            price_per_night: {
                required: "Room rent rate is required."
            },
            check_in: {
                required: "Check-in time is required."
            },
            expected_check_out: {
                required: "Check-out time is required."
            }
        }
    });

    // Revalidate payment mode when advance payment changes
    $("#advance_payment").on("input change", function() {
        $("#payment_mode").valid();
    });
});
</script>
@endsection
