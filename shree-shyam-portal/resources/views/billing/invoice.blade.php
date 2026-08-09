@extends('layouts.app')

@section('title', 'Stay Invoice')
@section('header_title', 'Stay Receipt')
@section('header_subtitle', 'Printable settlement invoice for stay #' . $stayRecord->id)

@section('content')
<div class="invoice-container animate-fade-in">
    <!-- Invoice Header -->
    <div class="invoice-header" style="align-items: center;">
        <div class="invoice-logo-title" style="display: flex; align-items: center; gap: 1.25rem;">
            <img src="{{ asset('logo_round.png') }}" alt="Shree Shyam Logo" style="width: 70px; height: 70px; object-fit: contain;">
            <div>
                <h2 style="font-family: var(--font-title); font-size: 1.8rem; color: #1e1b4b; font-weight: 800; margin: 0; line-height: 1.1;">SHREE SHYAM</h2>
                <p style="font-size: 0.85rem; font-weight: 800; color: #aa8443; margin: 0; letter-spacing: 1px; text-transform: uppercase;">HOTEL & RESTORENT</p>
                <p style="margin-top: 0.25rem; font-size: 0.8rem; color: #6b7280; line-height: 1.4; margin-bottom: 0;">Near Highway, Main Chowk, City Center<br>Phone: +91 98765 43210 | Email: contact@shreeshyamhotel.com</p>
            </div>
        </div>
        <div class="invoice-meta">
            <h3>INVOICE / RECEIPT</h3>
            <p><strong>Invoice No:</strong> #SS-{{ str_pad($stayRecord->id, 5, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Date:</strong> {{ date('d M Y') }}</p>
            <p><strong>Status:</strong> Paid</p>
        </div>
    </div>

    <!-- Invoice Details Grid (Guest & Stay info) -->
    <div class="invoice-details-grid">
        <div class="invoice-block">
            <h4>Billed To (Guest Profile)</h4>
            <p style="font-size: 1.05rem; margin-bottom: 0.25rem; color: #111827;">{{ $stayRecord->guest->name }}</p>
            <p>Phone: {{ $stayRecord->guest->phone }}</p>
            <p>Email: {{ $stayRecord->guest->email ?? 'N/A' }}</p>
            <p>ID Proof: {{ $stayRecord->guest->id_type }} ({{ $stayRecord->guest->id_number }})</p>
        </div>
        <div class="invoice-block" style="text-align: right;">
            <h4>Stay & Room Information</h4>
            <p style="font-size: 1.05rem; margin-bottom: 0.25rem; color: #111827;">Room {{ $stayRecord->room->room_number }}</p>
            <p>Type: {{ $stayRecord->room->room_type }}</p>
            <p>Check-in: {{ $stayRecord->check_in->format('d M Y, h:i A') }}</p>
            <p>Check-out: {{ $stayRecord->actual_check_out ? $stayRecord->actual_check_out->format('d M Y, h:i A') : date('d M Y, h:i A') }}</p>
        </div>
    </div>

    <!-- Bill Line Items Table -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: center;">Rate (per night)</th>
                <th style="text-align: center;">Qty (Nights)</th>
                <th style="text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Room Rent (Stay duration charges)</td>
                <td style="text-align: center;">₹{{ number_format($stayRecord->price_per_night, 2) }}</td>
                <td style="text-align: center;">{{ $stayRecord->nights }}</td>
                <td style="text-align: right;">₹{{ number_format($stayRecord->room_charges, 2) }}</td>
            </tr>
            
            @if($stayRecord->extraCharges->isNotEmpty())
                @foreach($stayRecord->extraCharges as $charge)
                    <tr>
                        <td>{{ $charge->description }}</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: center;">-</td>
                        <td style="text-align: right;">₹{{ number_format($charge->amount, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Billing Totals Settlement -->
    <div class="invoice-summary">
        <div class="invoice-summary-item">
            <span>Gross Total:</span>
            <span>₹{{ number_format($stayRecord->gross_total, 2) }}</span>
        </div>
        @if($stayRecord->discount > 0)
            <div class="invoice-summary-item" style="color: #059669;">
                <span>Discount Allowed:</span>
                <span>- ₹{{ number_format($stayRecord->discount, 2) }}</span>
            </div>
        @endif
        @if($stayRecord->tax_amount > 0)
            <div class="invoice-summary-item">
                <span>GST Tax Added:</span>
                <span>+ ₹{{ number_format($stayRecord->tax_amount, 2) }}</span>
            </div>
        @endif
        <div class="invoice-summary-item">
            <span>Net Payable:</span>
            <span>₹{{ number_format($stayRecord->net_total, 2) }}</span>
        </div>
        <div class="invoice-summary-item" style="border-top: 1px solid #e5e7eb; padding-top: 0.5rem;">
            <span>Advance Payment Received:</span>
            <span>₹{{ number_format($stayRecord->advance_payment, 2) }}</span>
        </div>
        <div class="invoice-summary-item grand-total">
            <span>Balance Settled ({{ $stayRecord->payment_mode ?? 'UPI' }}):</span>
            <span>₹{{ number_format(max(0, $stayRecord->net_total - $stayRecord->advance_payment), 2) }}</span>
        </div>
    </div>

    <!-- Invoice Footer -->
    <div class="invoice-footer">
        <p>Thank you for staying at Shree Shyam Hotel & Restaurant!</p>
        <p style="margin-top: 0.5rem; font-size: 0.75rem;">This is a computer generated receipt and does not require a physical signature.</p>
    </div>
</div>

<!-- Invoice Control Actions -->
<div class="invoice-actions">
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fa-solid fa-print"></i> Print Invoice / PDF
    </button>
</div>
@endsection
