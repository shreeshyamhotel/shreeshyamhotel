@extends('layouts.app')

@section('title', 'Guest Checkout')
@section('header_title', 'Check-out & Billing')
@section('header_subtitle', 'Finalize guest stay records and process invoice settlement')

@section('content')
<div class="checkout-grid">
    <!-- Left Column: Stay Details & Extra Charges -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Stay Summary -->
        <div class="glass-panel">
            <div class="section-header">
                <h2><i class="fa-solid fa-bed"></i> Stay Summary</h2>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.95rem;">
                <div class="d-flex justify-between" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
                    <span class="text-muted">Guest Name:</span>
                    <strong style="color: #fff;">{{ $stayRecord->guest->name }}</strong>
                </div>
                <div class="d-flex justify-between" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
                    <span class="text-muted">Room Assignment:</span>
                    <strong>Room {{ $stayRecord->room->room_number }} ({{ $stayRecord->room->room_type }})</strong>
                </div>
                <div class="d-flex justify-between" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
                    <span class="text-muted">Check-in Date:</span>
                    <span>{{ $stayRecord->check_in->format('d M Y, h:i A') }}</span>
                </div>
                <div class="d-flex justify-between" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
                    <span class="text-muted">Expected Check-out:</span>
                    <span>{{ $stayRecord->expected_check_out ? $stayRecord->expected_check_out->format('d M Y') : 'Not specified' }}</span>
                </div>
                <div class="d-flex justify-between" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
                    <span class="text-muted">Nights Count:</span>
                    <strong class="text-muted" style="color: #818cf8;">{{ $stayRecord->nights }} Night(s)</strong>
                </div>
                <div class="d-flex justify-between">
                    <span class="text-muted">Room Rent Rate:</span>
                    <strong>₹{{ number_format($stayRecord->price_per_night, 2) }} / Night</strong>
                </div>
            </div>
        </div>

        <!-- Extra Service / Restaurant Orders -->
        <div class="glass-panel">
            <div class="section-header">
                <h2><i class="fa-solid fa-utensils"></i> Restaurant & Extra Bills</h2>
                <span class="badge badge-info">₹{{ number_format($stayRecord->extra_charges_total, 2) }} Total</span>
            </div>

            <!-- Add Extra Charge Form -->
            <form id="extra-charge-form" action="{{ route('checkout.extra-charge', $stayRecord->id) }}" method="POST" style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--glass-border);">
                @csrf
                <div style="display: grid; grid-template-columns: 1.5fr 1fr 1fr; gap: 0.75rem; align-items: flex-end;">
                    <div class="form-group">
                        <label for="description" style="font-size: 0.8rem;">Charge Description</label>
                        <input type="text" name="description" id="description" class="form-control" placeholder="e.g. Restaurant Lunch Bill" required>
                    </div>
                    <div class="form-group">
                        <label for="amount" style="font-size: 0.8rem;">Amount (INR)</label>
                        <input type="number" name="amount" id="amount" class="form-control" placeholder="₹" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 42px;">
                        <i class="fa-solid fa-plus"></i> Add
                    </button>
                </div>
            </form>

            <!-- List of current extra charges -->
            @if($stayRecord->extraCharges->isEmpty())
                <p class="text-muted text-center py-3">No extra restaurant or room service bills added.</p>
            @else
                <table class="custom-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stayRecord->extraCharges as $charge)
                            <tr>
                                <td>{{ $charge->description }}</td>
                                <td style="text-align: right; color: #fff; font-weight: 600;">₹{{ number_format($charge->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Right Column: Settlement Summary & Checkout Form -->
    <div class="glass-panel billing-details-panel">
        <div class="section-header">
            <h2><i class="fa-solid fa-cash-register"></i> Bill Settlement</h2>
        </div>

        <form id="checkout-form" action="{{ route('checkout.store', $stayRecord->id) }}" method="POST">
            @csrf
            
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div class="billing-item">
                    <span>Room Charges ({{ $stayRecord->nights }} nights):</span>
                    <span>₹{{ number_format($stayRecord->room_charges, 2) }}</span>
                </div>
                
                <div class="billing-item">
                    <span>Restaurant / Extra Charges:</span>
                    <span>₹{{ number_format($stayRecord->extra_charges_total, 2) }}</span>
                </div>

                <div class="billing-item">
                    <span>Gross Amount:</span>
                    <strong style="color:#fff;">₹{{ number_format($stayRecord->gross_total, 2) }}</strong>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; border-bottom: 1px dashed var(--glass-border); padding-bottom: 1.25rem;">
                    <div class="form-group">
                        <label for="discount">Discount (INR)</label>
                        <input type="number" name="discount" id="discount" class="form-control" value="0" min="0" max="{{ $stayRecord->gross_total }}" oninput="recalculateBill()">
                    </div>
                    <div class="form-group">
                        <label for="tax_amount">Tax (GST) Amount</label>
                        <input type="number" name="tax_amount" id="tax_amount" class="form-control" value="0" min="0" oninput="recalculateBill()">
                    </div>
                </div>

                <div class="billing-item">
                    <span>Advance Payment Paid:</span>
                    <span style="color: var(--success-color); font-weight: 600;">- ₹{{ number_format($stayRecord->advance_payment, 2) }}</span>
                </div>

                <div class="billing-item balance">
                    <span>Balance Due (Outstanding):</span>
                    <span id="outstanding-val">₹{{ number_format($stayRecord->balance_due, 2) }}</span>
                </div>

                <div class="form-group" style="margin-top: 0.5rem;">
                    <label for="payment_mode">Settlement Payment Mode <span style="color: var(--danger-color);">*</span></label>
                    <select name="payment_mode" id="payment_mode" class="form-control" required>
                        <option value="Cash" {{ $stayRecord->payment_mode === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="UPI" {{ $stayRecord->payment_mode === 'UPI' ? 'selected' : '' }}>UPI / QR Code</option>
                        <option value="Card" {{ $stayRecord->payment_mode === 'Card' ? 'selected' : '' }}>Credit/Debit Card</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="room_status_after">Room Status After Checkout <span style="color: var(--danger-color);">*</span></label>
                    <select name="room_status_after" id="room_status_after" class="form-control" required>
                        <option value="Available" selected>Available (Ready for next check-in)</option>
                        <option value="Cleaning">Needs Cleaning</option>
                        <option value="Maintenance">Under Maintenance</option>
                    </select>
                </div>

                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-danger" style="width: 100%; font-size: 1.05rem; padding: 0.85rem;">
                        <i class="fa-solid fa-circle-check"></i> Complete Check-Out & Generate Invoice
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function recalculateBill() {
        const grossTotal = {{ $stayRecord->gross_total }};
        const advance = {{ $stayRecord->advance_payment }};
        
        const discountInput = document.getElementById('discount');
        const taxInput = document.getElementById('tax_amount');
        
        const discount = parseFloat(discountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        
        const netTotal = grossTotal - discount + tax;
        const balanceDue = netTotal - advance;
        
        document.getElementById('outstanding-val').textContent = '₹' + balanceDue.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
</script>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Validate Extra Charge Form
    $("#extra-charge-form").validate({
        rules: {
            description: {
                required: true,
                minlength: 3,
                maxlength: 100
            },
            amount: {
                required: true,
                number: true,
                min: 0
            }
        },
        messages: {
            description: {
                required: "Please enter charge description.",
                minlength: "Description must be at least 3 characters."
            },
            amount: {
                required: "Please enter charge amount.",
                number: "Please enter a valid numeric value.",
                min: "Amount cannot be negative."
            }
        }
    });

    // Validate Final Settlement Form
    $("#checkout-form").validate({
        rules: {
            discount: {
                number: true,
                min: 0,
                max: {{ $stayRecord->gross_total }}
            },
            tax_amount: {
                number: true,
                min: 0
            },
            payment_mode: {
                required: true
            },
            room_status_after: {
                required: true
            }
        },
        messages: {
            discount: {
                number: "Please enter a valid numeric discount.",
                min: "Discount cannot be negative.",
                max: "Discount cannot exceed the gross bill amount of ₹{{ $stayRecord->gross_total }}."
            },
            tax_amount: {
                number: "Please enter a valid numeric tax amount.",
                min: "Tax cannot be negative."
            },
            payment_mode: {
                required: "Please select payment settlement mode."
            },
            room_status_after: {
                required: "Please select status for the room after checkout."
            }
        }
    });
});
</script>
@endsection
