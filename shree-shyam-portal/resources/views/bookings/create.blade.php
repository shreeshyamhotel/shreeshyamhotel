@extends('layouts.app')

@section('title', 'Create Booking')
@section('header_title', 'New Advance Booking')
@section('header_subtitle', 'Record future reservation details and check room availability')

@section('content')
<div class="glass-panel">
    <div class="section-header">
        <h2><i class="fa-solid fa-calendar-plus"></i> Reservation Booking Form</h2>
        <span class="text-muted"><span style="color: var(--danger-color);">*</span> Indicates required field</span>
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

    <form id="booking-form" action="{{ route('bookings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="checkout-grid">
            <!-- Left Side: Guest Details -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #818cf8; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-user"></i> Guest Personal Information
                </h3>
                
                <div class="form-group">
                    <label for="phone">Phone Number <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="10-digit mobile number" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label for="name">Full Name <span style="color: var(--danger-color);">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Guest's full name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" name="city" id="city" class="form-control" placeholder="e.g. Jaipur" value="{{ old('city') }}">
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" name="state" id="state" class="form-control" placeholder="e.g. Rajasthan" value="{{ old('state') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Full Address</label>
                    <textarea name="address" id="address" class="form-control" placeholder="Enter guest's residential address">{{ old('address') }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="id_type">ID Proof Type <span style="color: var(--danger-color);">*</span></label>
                        <select name="id_type" id="id_type" class="form-control" required>
                            <option value="Aadhar Card" {{ old('id_type') === 'Aadhar Card' ? 'selected' : '' }}>Aadhar Card</option>
                            <option value="PAN Card" {{ old('id_type') === 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                            <option value="Passport" {{ old('id_type') === 'Passport' ? 'selected' : '' }}>Passport</option>
                            <option value="Voter ID" {{ old('id_type') === 'Voter ID' ? 'selected' : '' }}>Voter ID</option>
                            <option value="Driving License" {{ old('id_type') === 'Driving License' ? 'selected' : '' }}>Driving License</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_number">ID Number <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="id_number" id="id_number" class="form-control" placeholder="Enter ID number" value="{{ old('id_number') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_proof">Upload ID Document (Image/PDF)</label>
                    <input type="file" name="id_proof" id="id_proof" class="form-control" accept="image/*,application/pdf">
                </div>
            </div>

            <!-- Right Side: Stay Information -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #818cf8; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-calendar-check"></i> Stay & Room Information
                </h3>

                <div class="form-group">
                    <label for="room_id">Assign Room <span style="color: var(--danger-color);">*</span></label>
                    <select name="room_id" id="room_id" class="form-control" required>
                        <option value="" disabled selected>Choose Room for Booking</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                Room {{ $room->room_number }} - {{ $room->room_type }} (₹{{ number_format($room->price_per_night) }}/night) [{{ $room->status }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="adults">Adults <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="adults" id="adults" class="form-control" min="1" max="10" value="{{ old('adults', 1) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="children">Children <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="children" id="children" class="form-control" min="0" max="10" value="{{ old('children', 0) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="check_in">Booked Check-In Date & Time <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="check_in" id="check_in" class="form-control" placeholder="Select Date & Time" value="{{ old('check_in') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="expected_check_out">Booked Check-Out Date & Time <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="expected_check_out" id="expected_check_out" class="form-control" placeholder="Select Date & Time" value="{{ old('expected_check_out') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose of Visit</label>
                    <input type="text" name="purpose" id="purpose" class="form-control" placeholder="e.g. Business Travel" value="{{ old('purpose') }}">
                </div>

                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #818cf8; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; margin-top: 0.5rem;">
                    <i class="fa-solid fa-money-bill-wave"></i> Booking Advance Payment
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="advance_payment">Advance Paid (INR)</label>
                        <input type="number" name="advance_payment" id="advance_payment" class="form-control" placeholder="e.g. 1000" value="{{ old('advance_payment', 0) }}" min="0">
                    </div>
                    <div class="form-group">
                        <label for="payment_mode">Payment Mode</label>
                        <select name="payment_mode" id="payment_mode" class="form-control">
                            <option value="" selected>Select Mode</option>
                            <option value="Cash" {{ old('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="UPI" {{ old('payment_mode') === 'UPI' ? 'selected' : '' }}>UPI / QR Code</option>
                            <option value="Card" {{ old('payment_mode') === 'Card' ? 'selected' : '' }}>Credit/Debit Card</option>
                        </select>
                    </div>
                </div>

                <div class="actions-group" style="margin-top: 2rem; justify-content: flex-end;">
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Cancel Booking</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-calendar-check"></i> Save Advance Booking
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Custom phone validator (Indian Mobile)
    $.validator.addMethod("indianPhone", function(value, element) {
        return this.optional(element) || /^[6-9]\d{9}$/.test(value);
    }, "Please enter a valid 10-digit mobile number starting with 6-9.");

    // Custom date/time validator
    $.validator.addMethod("afterCheckin", function(value, element) {
        if (!value) return true;
        var checkinVal = $("#check_in").val();
        if (!checkinVal) return true;
        var checkinDate = new Date(checkinVal);
        var checkoutDate = new Date(value);
        return checkoutDate > checkinDate;
    }, "Booked check-out must be after check-in date & time.");

    // Dynamic ID Validator
    $.validator.addMethod("idNumberValidator", function(value, element) {
        var idType = $("#id_type").val();
        var cleanVal = value.replace(/[\s-]/g, '').toUpperCase();
        
        if (idType === "Aadhar Card") {
            return /^\d{12}$/.test(cleanVal);
        } else if (idType === "PAN Card") {
            return /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(cleanVal);
        } else if (idType === "Passport") {
            return /^[A-Z][0-9]{7}$/.test(cleanVal);
        } else if (idType === "Voter ID") {
            return /^[A-Z]{3}[0-9]{7}$/.test(cleanVal) || /^[A-Z0-9]{10}$/.test(cleanVal);
        } else if (idType === "Driving License") {
            return /^[A-Z]{2}[0-9]{13}$/.test(cleanVal);
        }
        return true;
    }, function() {
        var idType = $("#id_type").val();
        if (idType === "Aadhar Card") return "Please enter a valid 12-digit Aadhar number.";
        if (idType === "PAN Card") return "Please enter a valid 10-character PAN.";
        if (idType === "Passport") return "Please enter a valid Passport number.";
        if (idType === "Voter ID") return "Please enter a valid Voter ID number.";
        if (idType === "Driving License") return "Please enter a valid 15-character Central DL number.";
        return "Please enter a valid ID card number.";
    });

    // Initialize Flatpickr Date-Time Pickers
    var checkinPicker = flatpickr("#check_in", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today", // Booking must be future/today
        onChange: function(selectedDates, dateStr, instance) {
            checkoutPicker.set("minDate", dateStr);
            $("#check_in").valid();
        }
    });

    var checkoutPicker = flatpickr("#expected_check_out", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            $("#expected_check_out").valid();
        }
    });

    $("#booking-form").validate({
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            phone: {
                required: true,
                indianPhone: true
            },
            email: {
                email: true
            },
            id_type: {
                required: true
            },
            id_number: {
                required: true,
                idNumberValidator: true
            },
            room_id: {
                required: true
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
            email: {
                email: "Please enter a valid email."
            },
            id_type: {
                required: "Please select ID proof type."
            },
            id_number: {
                required: "ID document number is required."
            },
            room_id: {
                required: "Please select room to book."
            },
            check_in: {
                required: "Booking check-in date is required."
            },
            expected_check_out: {
                required: "Booking check-out date is required."
            },
            adults: {
                required: "Adults count is required."
            },
            children: {
                required: "Children count is required."
            },
            advance_payment: {
                number: "Please enter a valid decimal number.",
                min: "Advance payment cannot be negative."
            },
            payment_mode: {
                required: "Please select payment mode for advance payment."
            }
        }
    });

    // Revalidate ID Number on Type change
    $("#id_type").change(function() {
        $("#id_number").val('');
        $("#id_number").valid();
    });

    // Revalidate payment mode when advance payment changes
    $("#advance_payment").on("input change", function() {
        $("#payment_mode").valid();
    });
});
</script>
@endsection
