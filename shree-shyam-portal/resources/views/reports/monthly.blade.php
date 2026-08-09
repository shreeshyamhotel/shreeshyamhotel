@extends('layouts.app')

@section('title', 'Monthly Collection Report')
@section('header_title', 'Monthly Collection Report')
@section('header_subtitle', 'Track stays, advance bookings, and total collections for a specific month')

@section('content')
<div class="glass-panel" style="margin-bottom: 2rem;">
    <!-- Month Selection Form -->
    <form action="{{ route('reports.monthly') }}" method="GET" style="display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            <h3 style="font-family: var(--font-title); font-size: 1.1rem; color: #fff; margin-bottom: 0.25rem;">
                <i class="fa-solid fa-calendar-check"></i> Select Report Month
            </h3>
            <p class="text-muted" style="font-size: 0.8rem; margin: 0;">Change the month below to load collections for that month</p>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <label for="month" style="color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Month:</label>
            <input type="text" name="month" id="month" class="form-control" value="{{ $selectedMonth }}" style="width: auto; max-width: 220px; font-weight: 600; text-align: center; cursor: pointer; background-color: rgba(11, 15, 25, 0.8);">
        </div>
    </form>
</div>

<!-- Aggregates Grid -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="glass-panel stat-card">
        <div class="stat-icon info">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <h4>Total Stays / Bookings</h4>
            <div class="stat-value">{{ $totalStays }}</div>
        </div>
    </div>
    
    <div class="glass-panel stat-card">
        <div class="stat-icon success">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h4>Total Advance Paid</h4>
            <div class="stat-value">₹{{ number_format($totalAdvance, 2) }}</div>
        </div>
    </div>

    <div class="glass-panel stat-card">
        <div class="stat-icon warning">
            <i class="fa-solid fa-tags"></i>
        </div>
        <div class="stat-info">
            <h4>Total Discounts Given</h4>
            <div class="stat-value">₹{{ number_format($totalDiscounts, 2) }}</div>
        </div>
    </div>
    
    <div class="glass-panel stat-card" style="border: 1px solid rgba(52, 211, 153, 0.4); box-shadow: 0 4px 20px rgba(52, 211, 153, 0.15);">
        <div class="stat-icon" style="background-color: rgba(52, 211, 153, 0.15); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.35);">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div class="stat-info">
            <h4 style="color: #34d399; font-weight: 700;">Total Net Collection</h4>
            <div class="stat-value" style="color: #34d399; font-size: 1.6rem; font-weight: 800;">₹{{ number_format($totalCollection, 2) }}</div>
        </div>
    </div>
</div>

<!-- Detailed Table Panel -->
<div class="glass-panel">
    <div class="section-header">
        <h2><i class="fa-solid fa-file-invoice-dollar"></i> Stay & Collection Details for {{ \Carbon\Carbon::parse($selectedMonth . '-01')->format('F Y') }}</h2>
    </div>

    @if($records->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fa-solid fa-circle-info fa-3x mb-4 text-muted"></i>
            <p>No stays or bookings found checking in during this month.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Guest Details</th>
                        <th>Room No.</th>
                        <th>Stay Period</th>
                        <th>Nights in {{ \Carbon\Carbon::parse($selectedMonth . '-01')->format('M') }}</th>
                        <th>Room Rent (Apportioned)</th>
                        <th>Extras in Month</th>
                        <th>Advance in Month</th>
                        <th>Settlement in Month</th>
                        <th style="text-align: right;">Total in Month</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>
                                <strong style="color: #fff; font-size: 0.95rem;">{{ $record->guest->name }}</strong>
                                <small class="text-muted" style="display: block; margin-top: 2px;">
                                    <i class="fa-solid fa-phone" style="font-size: 0.75rem;"></i> {{ $record->guest->phone }}
                                </small>
                            </td>
                            <td>
                                <span class="badge badge-info" style="font-size: 0.8rem; padding: 0.3rem 0.5rem;">
                                    Room {{ $record->room->room_number }}
                                </span>
                            </td>
                            <td>
                                <small style="display: block; color: var(--text-primary);"><strong>In:</strong> {{ $record->check_in->format('d M Y') }}</small>
                                <small style="display: block; color: var(--text-secondary);"><strong>Out:</strong> {{ $record->actual_check_out ? $record->actual_check_out->format('d M Y') : 'Active' }}</small>
                            </td>
                            <td>
                                <strong style="color: #fff;">{{ $record->nights_in_month }}</strong> <span class="text-muted">/ {{ $record->nights }} Nights</span>
                            </td>
                            <td>₹{{ number_format($record->room_charges_in_month, 2) }}</td>
                            <td>₹{{ number_format($record->extra_charges_in_month, 2) }}</td>
                            <td>₹{{ number_format($record->advance_in_month, 2) }}</td>
                            <td>₹{{ number_format($record->settlement_in_month, 2) }}</td>
                            <td style="text-align: right; font-weight: 700; color: #34d399;">
                                ₹{{ number_format($record->net_collection_in_month, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <!-- Bottom Highlight Total Row -->
                    <tr style="background-color: rgba(52, 211, 153, 0.05); border-top: 2px solid rgba(52, 211, 153, 0.25);">
                        <td colspan="4" style="font-weight: 800; color: #34d399; font-size: 1rem; text-transform: uppercase; padding: 1.25rem 1rem;">
                            Total Monthly Collection
                        </td>
                        <td style="font-weight: 700; color: #fff;">₹{{ number_format($totalRoomCharges, 2) }}</td>
                        <td style="font-weight: 700; color: #fff;">₹{{ number_format($totalExtraCharges, 2) }}</td>
                        <td style="font-weight: 700; color: #fff;">₹{{ number_format($totalAdvance, 2) }}</td>
                        <td style="font-weight: 700; color: #fff;">
                            ₹{{ number_format($totalCollection - $totalAdvance, 2) }}
                        </td>
                        <td style="text-align: right; font-weight: 800; color: #34d399; font-size: 1.1rem; padding: 1.25rem 1rem;">
                            ₹{{ number_format($totalCollection, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    flatpickr("#month", {
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m", // Sent to backend
                altFormat: "F Y", // Displayed (e.g. August 2026)
                theme: "dark"
            })
        ],
        maxDate: new Date(), // Enforce restriction: no future months allowed
        onChange: function(selectedDates, dateStr, instance) {
            instance.element.form.submit();
        }
    });
});
</script>
@endsection
