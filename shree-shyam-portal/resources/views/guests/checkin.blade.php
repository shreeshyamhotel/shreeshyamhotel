@extends('layouts.app')

@section('title', 'Guest Check-In')
@section('header_title', 'New Guest Check-In')
@section('header_subtitle', 'Record details of arriving guest and assign an available room')

@section('content')
<div class="glass-panel">
    <div class="section-header">
        <h2><i class="fa-solid fa-hotel"></i> Check-In Form</h2>
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

    <form id="checkin-form" action="{{ route('checkin.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Entry Type Segment Toggle -->
        <div class="glass-panel mb-4" style="padding: 1.25rem 1.75rem; border-color: rgba(79, 70, 229, 0.25);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-family: var(--font-title); font-size: 1.1rem; font-weight: 600; color: #fff;">
                        <i class="fa-solid fa-sliders"></i> Select Operation Type
                    </h3>
                    <p class="text-muted" style="font-size: 0.8rem; margin-top: 1px;">Choose between immediate walk-in check-in or booking for a future date</p>
                </div>
                <div style="display: flex; gap: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #fff; font-weight: 600; font-size: 0.95rem;">
                        <input type="radio" name="entry_type" id="entry_type_checkin" value="checkin" {{ old('entry_type', $entryType ?? 'checkin') === 'checkin' ? 'checked' : '' }} style="accent-color: var(--accent-color); width: 20px; height: 20px;">
                        <span>Instant Check-In Now</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #fff; font-weight: 600; font-size: 0.95rem;">
                        <input type="radio" name="entry_type" id="entry_type_booking" value="booking" {{ old('entry_type', $entryType ?? 'checkin') === 'booking' ? 'checked' : '' }} style="accent-color: var(--accent-color); width: 20px; height: 20px;">
                        <span>Advance Reservation Booking</span>
                    </label>
                </div>
            </div>
        </div>

        <div id="lookup-alert" class="alert alert-success animate-fade-in" style="display: none; margin-bottom: 1.5rem; padding: 0.75rem 1.25rem; border-color: rgba(16, 185, 129, 0.35);">
            <i class="fa-solid fa-circle-check" style="color: #34d399; font-size: 1.3rem;"></i>
            <div class="alert-content">
                <strong style="color: #fff;">Welcome Back Guest!</strong> Profile details found and auto-filled.
            </div>
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>

        <div class="checkout-grid">
            <!-- Left Side: Guest Details -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #818cf8; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-user"></i> Guest Personal Information
                </h3>
                
                <!-- ID Proof Type & Number FIRST as requested -->
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

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="name">Full Name <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Guest's full name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="10-digit mobile number" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label id="id-proof-label" for="id_proof">Upload ID Document(s) <span style="color: var(--danger-color);">*</span></label>
                        <input type="file" name="id_proof[]" id="id_proof" class="form-control" accept="image/*,application/pdf" multiple required>
                        <small class="form-help" id="id-proof-preview-link">Max size 4MB. Accepted: jpeg, png, pdf (Hold Ctrl to select multiple)</small>
                    </div>
                </div>
            </div>

            <!-- Right Side: Stay Information -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #818cf8; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                    <i class="fa-solid fa-key"></i> Room & Stay Details
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="room_id" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <span>Assign Room <span style="color: var(--danger-color);">*</span></span>
                            <a href="#" id="check-room-availability-btn" style="color: #818cf8; font-size: 0.8rem; font-weight: 600; text-decoration: underline; display: none;"><i class="fa-solid fa-clock-rotate-left"></i> Check Availability</a>
                        </label>
                        @if($rooms->isEmpty())
                            <div class="alert alert-danger" style="margin-bottom: 0; padding: 0.75rem 1rem;">
                                <i class="fa-solid fa-circle-xmark"></i>
                                <div class="alert-content">
                                    No rooms are registered. <a href="{{ route('rooms.create') }}" style="color: #fff; font-weight: 700;">Add a room</a> first.
                                </div>
                            </div>
                        @else
                            <select name="room_id" id="room_id" class="form-control" required>
                                <option value="" disabled selected>Choose Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-price="{{ $room->price_per_night }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }} - {{ $room->room_type }} [{{ $room->status }}]
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="price_per_night">Room Rent Per Night (₹) <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="price_per_night" id="price_per_night" class="form-control" placeholder="Rent rate (INR)" value="{{ old('price_per_night') }}" min="0" required>
                    </div>
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
                        <label id="check_in_label" for="check_in">Check-In Date & Time <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="check_in" id="check_in" class="form-control" placeholder="Select Date & Time" value="{{ old('check_in', now()->format('Y-m-d H:i')) }}" required>
                    </div>
                    <div class="form-group">
                        <label id="check_out_label" for="expected_check_out">Expected Check-Out Date & Time</label>
                        <input type="text" name="expected_check_out" id="expected_check_out" class="form-control" placeholder="Select Date & Time" value="{{ old('expected_check_out') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="purpose">Purpose of Visit</label>
                    <input type="text" name="purpose" id="purpose" class="form-control" placeholder="e.g. Tourism, Business, Personal" value="{{ old('purpose') }}">
                </div>

                <h3 style="font-family: var(--font-title); font-size: 1.15rem; color: #818cf8; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem; margin-bottom: 0.5rem; margin-top: 0.5rem;">
                    <i class="fa-solid fa-rupee-sign"></i> Advance Payment
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
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" id="submit-btn" class="btn btn-success">
                        <i class="fa-solid fa-check-double"></i> Complete Check-In
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal HTML for Room Availability Check -->
<div id="availability-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.75); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div class="glass-panel" style="width: 95%; max-width: 500px; padding: 2rem; border: 1px solid var(--glass-border); box-shadow: 0 8px 32px 0 rgba(0,0,0,0.5); display: flex; flex-direction: column; gap: 1.5rem; background: rgba(15, 23, 42, 0.95);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border); padding-bottom: 0.75rem; margin-bottom: 0.5rem;">
            <h3 id="modal-title" style="font-family: var(--font-title); font-size: 1.25rem; color: #fff; margin: 0;"><i class="fa-solid fa-hotel"></i> Room Availability Timeline</h3>
            <button type="button" class="close-modal-btn" style="background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; line-height: 1;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="modal-content" style="max-height: 350px; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem;">
            <!-- Loaded dynamically -->
        </div>
        <div style="text-align: right; border-top: 1px solid var(--glass-border); padding-top: 1rem; margin-top: 0.5rem;">
            <button type="button" class="btn btn-secondary close-modal-btn">Close</button>
        </div>
    </div>
</div>

<!-- AJAX Helper to auto-complete guest profile on returning guest ID search -->
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Custom phone validator (Indian Mobile)
    $.validator.addMethod("indianPhone", function(value, element) {
        return this.optional(element) || /^[6-9]\d{9}$/.test(value);
    }, "Please enter a valid 10-digit mobile number starting with 6-9.");

    // Custom date/time validators
    $.validator.addMethod("noFutureDate", function(value, element) {
        if (!value) return true;
        var selectedDate = new Date(value);
        var now = new Date();
        // Allow a small 1-minute buffer for submission delay
        return selectedDate <= new Date(now.getTime() + 60000);
    }, "Check-in date and time cannot be in the future.");

    $.validator.addMethod("afterCheckin", function(value, element) {
        if (!value) return true;
        var checkinVal = $("#check_in").val();
        if (!checkinVal) return true;
        var checkinDate = new Date(checkinVal);
        var checkoutDate = new Date(value);
        return checkoutDate > checkinDate;
    }, "Expected check-out date & time must be after check-in.");

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
        if (idType === "Aadhar Card") return "Please enter a valid 12-digit Aadhar number (digits only).";
        if (idType === "PAN Card") return "Please enter a valid 10-character PAN (e.g. ABCDE1234F).";
        if (idType === "Passport") return "Please enter a valid Passport number (1 letter + 7 digits).";
        if (idType === "Voter ID") return "Please enter a valid Voter ID number.";
        if (idType === "Driving License") return "Please enter a valid 15-character Central DL number.";
        return "Please enter a valid ID card number.";
    });

    // Initialize Flatpickr Date-Time Pickers
    var checkinPicker = flatpickr("#check_in", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onChange: function(selectedDates, dateStr, instance) {
            checkoutPicker.set("minDate", dateStr);
            $("#check_in").valid();
        }
    });

    var checkoutPicker = flatpickr("#expected_check_out", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        onChange: function(selectedDates, dateStr, instance) {
            $("#expected_check_out").valid();
        }
    });

    // Initialize Validator
    $("#checkin-form").validate({
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
            "id_proof[]": {
                required: function() {
                    return !isAutoPopulated;
                }
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
                required: "Guest name is required.",
                minlength: "Name must be at least 3 characters."
            },
            phone: {
                required: "Phone number is required."
            },
            email: {
                email: "Please enter a valid email address."
            },
            id_type: {
                required: "Please select an ID proof type."
            },
            id_number: {
                required: "ID document number is required."
            },
            "id_proof[]": {
                required: "At least one ID proof document is required."
            },
            room_id: {
                required: "Please assign a room to the guest."
            },
            price_per_night: {
                required: "Room rent rate is required.",
                number: "Please enter a valid numeric rent."
            },
            check_in: {
                required: "Check-in date and time is required."
            },
            adults: {
                required: "Adults count is required.",
                min: "At least 1 adult is required to stay."
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

    var isAutoPopulated = false;

    function updateIdProofRequiredState() {
        if (isAutoPopulated) {
            $("#id-proof-label").html('Upload ID Document(s) <small class="text-muted">(Optional - Returning Guest)</small>');
            $("#id_proof").removeAttr('required');
        } else {
            $("#id-proof-label").html('Upload ID Document(s) <span style="color: var(--danger-color);">*</span>');
            $("#id_proof").attr('required', 'required');
        }
    }

    function clearGuestFields() {
        $("#name").val('');
        $("#phone").val('');
        $("#email").val('');
        $("#id-proof-preview-link").html("Max size 4MB. Accepted: jpeg, png, pdf (Hold Ctrl to select multiple)");
        $("#lookup-alert").slideUp(200);
        isAutoPopulated = false;
        updateIdProofRequiredState();
    }

    function resetGuestFields() {
        if (isAutoPopulated) {
            clearGuestFields();
        }
    }

    // Revalidate ID Number on Type change
    $("#id_type").change(function() {
        $("#id_number").val(''); // Clear old value to ensure clean check
        clearGuestFields(); // Force clear all inputs
        $("#id_number").valid();
    });

    // Revalidate payment mode when advance payment changes
    $("#advance_payment").on("input change", function() {
        $("#payment_mode").valid();
    });

    // Populate rent rate when room is selected
    $("#room_id").change(function() {
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price') || 0;
        $("#price_per_night").val(price);
        $("#price_per_night").valid();
        
        if ($(this).val()) {
            $("#check-room-availability-btn").fadeIn(200);
        } else {
            $("#check-room-availability-btn").fadeOut(200);
        }
    });

    if ($("#room_id").val()) {
        $("#check-room-availability-btn").show();
    }

    // Close availability modal
    $(".close-modal-btn").click(function() {
        $("#availability-modal").fadeOut(200);
    });

    // Check availability click handler
    $("#check-room-availability-btn").click(function(e) {
        e.preventDefault();
        var roomId = $("#room_id").val();
        if (!roomId) return;
        
        $.ajax({
            url: "/rooms/" + roomId + "/availability",
            type: "GET",
            dataType: "json",
            success: function(data) {
                $("#modal-title").html(`<i class="fa-solid fa-hotel"></i> Room ${data.room_number} Status`);
                
                let contentHtml = '';
                
                // Status color map
                let statusColor = '#34d399'; // Available
                if (data.room_status === 'Occupied') statusColor = '#f87171';
                else if (data.room_status === 'Reserved') statusColor = '#fb923c';
                else if (data.room_status === 'Cleaning') statusColor = '#fde047';
                else if (data.room_status === 'Maintenance') statusColor = '#fb923c';
                
                contentHtml += `
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); padding: 0.75rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                        <span class="text-muted" style="font-weight: 500; font-size: 0.9rem;">Current Status</span>
                        <span style="color: ${statusColor}; font-weight: 700; text-transform: uppercase; font-size: 0.9rem;">${data.room_status}</span>
                    </div>
                `;
                
                if (data.is_occupied && data.active_stay) {
                    contentHtml += `
                        <div style="background: rgba(248, 113, 113, 0.05); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(248, 113, 113, 0.25);">
                            <div style="color: #f87171; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-exclamation"></i> Currently Occupied
                            </div>
                            <div style="font-size: 0.85rem; color: #fff; line-height: 1.5;">
                                <strong>Guest:</strong> ${data.active_stay.guest_name}<br>
                                <strong>Available From:</strong> ${data.active_stay.check_out}
                            </div>
                        </div>
                    `;
                } else if (data.is_reserved_now && data.reserved_stay_now) {
                    contentHtml += `
                        <div style="background: rgba(251, 146, 60, 0.05); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(251, 146, 60, 0.25);">
                            <div style="color: #fb923c; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-circle-exclamation"></i> Currently Reserved / Booked
                            </div>
                            <div style="font-size: 0.85rem; color: #fff; line-height: 1.5;">
                                <strong>Reserved For:</strong> ${data.reserved_stay_now.guest_name}<br>
                                <strong>Booking Period:</strong> ${data.reserved_stay_now.check_in} to ${data.reserved_stay_now.check_out}
                            </div>
                        </div>
                    `;
                } else {
                    contentHtml += `
                        <div style="background: rgba(52, 211, 153, 0.05); padding: 1rem; border-radius: var(--radius-sm); border: 1px solid rgba(52, 211, 153, 0.25);">
                            <div style="color: #34d399; font-weight: 700; font-size: 0.9rem;">
                                <i class="fa-solid fa-circle-check"></i> Room is Available for Instant Check-In
                            </div>
                        </div>
                    `;
                }
                
                // Timeline List
                contentHtml += `<div style="font-weight: 600; color: #fff; font-size: 0.95rem; margin-top: 0.5rem; margin-bottom: 0.25rem;">Schedule & Reservations</div>`;
                
                if (data.timeline.length === 0) {
                    contentHtml += `<p class="text-muted" style="font-size: 0.85rem; font-style: italic; margin: 0;">No active stays or future reservations scheduled.</p>`;
                } else {
                    contentHtml += `<div style="display: flex; flex-direction: column; gap: 0.75rem;">`;
                    data.timeline.forEach(function(item) {
                        let badgeClass = item.status === 'Checked-In' ? 'badge-success' : 'badge-warning';
                        contentHtml += `
                            <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: var(--radius-sm);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                    <strong style="color: #fff; font-size: 0.85rem;">${item.guest_name}</strong>
                                    <span class="badge ${badgeClass}" style="font-size: 0.7rem; padding: 0.15rem 0.4rem;">${item.status}</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                                    <strong>In:</strong> ${item.check_in}<br>
                                    <strong>Out:</strong> ${item.check_out}
                                </div>
                            </div>
                        `;
                    });
                    contentHtml += `</div>`;
                }
                
                $("#modal-content").html(contentHtml);
                $("#availability-modal").css("display", "flex").hide().fadeIn(200);
            }
        });
    });

    // Dynamic UI configuration on Entry Type Toggle
    function handleEntryTypeToggle() {
        var mode = $('input[name="entry_type"]:checked').val();
        if (mode === 'booking') {
            $("#submit-btn").html('<i class="fa-solid fa-calendar-check"></i> Complete Reservation Booking');
            $("#submit-btn").removeClass('btn-success').addClass('btn-primary');
            $("#check_in_label").html('Booked Check-In Date & Time <span style="color: var(--danger-color);">*</span>');
            $("#check_out_label").html('Booked Check-Out Date & Time <span style="color: var(--danger-color);">*</span>');
            
            // Reconfigure Pickers
            checkinPicker.set("maxDate", null);
            checkinPicker.set("minDate", "today");
            checkoutPicker.set("minDate", "today");
            
            // Update Validation rules
            $("#check_in").rules("remove", "noFutureDate");
            $("#expected_check_out").rules("add", {
                required: true,
                messages: {
                    required: "Booking check-out date is required."
                }
            });
        } else {
            $("#submit-btn").html('<i class="fa-solid fa-check-double"></i> Complete Check-In');
            $("#submit-btn").removeClass('btn-primary').addClass('btn-success');
            $("#check_in_label").html('Check-In Date & Time <span style="color: var(--danger-color);">*</span>');
            $("#check_out_label").html('Expected Check-Out Date & Time');
            
            // Reconfigure Pickers
            checkinPicker.set("maxDate", new Date());
            checkinPicker.set("minDate", null);
            checkoutPicker.set("minDate", "today");
            
            // Update Validation rules
            $("#expected_check_out").rules("remove", "required");
            $("#check_in").rules("add", {
                noFutureDate: true
            });
        }
    }

    $('input[name="entry_type"]').change(handleEntryTypeToggle);
    handleEntryTypeToggle(); // Run on startup

    // AJAX LOOKUP ON ID VERIFICATION (Matches ID Type + ID Number)
    function lookupGuest() {
        var idType = $("#id_type").val();
        var idNumber = $("#id_number").val().trim();
        
        // Only lookup if ID number is valid for that type
        if (idNumber.length >= 4 && $("#id_number").valid()) {
            $.ajax({
                url: "{{ route('guests.lookup') }}",
                type: "GET",
                data: {
                    id_type: idType,
                    id_number: idNumber
                },
                dataType: "json",
                success: function(data) {
                    if (data.found) {
                        // Populate details
                        $("#name").val(data.guest.name);
                        $("#phone").val(data.guest.phone);
                        $("#email").val(data.guest.email);

                        // If user uploaded an ID previously, show viewing links
                        if (data.guest.id_proof_paths && data.guest.id_proof_paths.length > 0) {
                            let linksHtml = '';
                            data.guest.id_proof_paths.forEach(function(path, index) {
                                linksHtml += `<a href="${path}" target="_blank" style="color: #818cf8; font-weight: 600; text-decoration: underline; margin-right: 1.5rem;">` +
                                    `<i class="fa-solid fa-file-invoice"></i> View ID ${index + 1}</a>`;
                            });
                            $("#id-proof-preview-link").html(linksHtml);
                        } else {
                            $("#id-proof-preview-link").html("Max size 4MB. Accepted: jpeg, png, pdf (Hold Ctrl to select multiple)");
                        }

                        // Set flag
                        isAutoPopulated = true;
                        updateIdProofRequiredState();

                        // Re-trigger validations to clear error tags
                        $("#name, #phone, #email").valid();

                        // Visual Alert
                        $("#lookup-alert").hide().slideDown(300);
                        setTimeout(function() {
                            $("#lookup-alert").slideUp(300);
                        }, 5000);
                    }
                }
            });
        }
    }

    $("#id_number").on("blur change", lookupGuest);
    $("#id_number").on("input", resetGuestFields);
});
</script>
@endsection
