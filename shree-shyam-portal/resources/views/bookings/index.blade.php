@extends('layouts.app')

@section('title', 'Bookings & Reservations')
@section('header_title', 'Stay Records & Reservations')
@section('header_subtitle', 'Monitor current guest stays and future advance bookings')

@section('content')
<div class="glass-panel">
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1.5rem; flex-wrap: wrap;">
        <!-- Tabs for status filtering -->
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('bookings.index', ['status' => 'ActiveBooked']) }}" class="btn {{ $statusFilter === 'ActiveBooked' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                <i class="fa-solid fa-hotel"></i> Current & Upcoming Stays
            </a>
            <a href="{{ route('bookings.index', ['status' => 'Booked']) }}" class="btn {{ $statusFilter === 'Booked' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                <i class="fa-solid fa-calendar-days"></i> Only Reserved
            </a>
            <a href="{{ route('bookings.index', ['status' => 'Active']) }}" class="btn {{ $statusFilter === 'Active' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                <i class="fa-solid fa-user-check"></i> Only Checked-In
            </a>
            <a href="{{ route('bookings.index', ['status' => 'Completed']) }}" class="btn {{ $statusFilter === 'Completed' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                <i class="fa-solid fa-circle-check"></i> Completed
            </a>
            <a href="{{ route('bookings.index', ['status' => 'Cancelled']) }}" class="btn {{ $statusFilter === 'Cancelled' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                <i class="fa-solid fa-circle-xmark"></i> Cancelled
            </a>
            <a href="{{ route('bookings.index', ['status' => 'All']) }}" class="btn {{ $statusFilter === 'All' ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                <i class="fa-solid fa-list"></i> All Records
            </a>
        </div>
        
        <a href="{{ route('checkin.form', ['entry_type' => 'booking']) }}" class="btn btn-primary">
            <i class="fa-solid fa-plus-circle"></i> Create Advance Booking
        </a>
    </div>

    <!-- ActiveBooked Mode: Both tables on one page -->
    @if($statusFilter === 'ActiveBooked')
        
        <!-- SECTION 1: ACTIVE CHECK-INS -->
        <div class="mb-5">
            <h3 style="font-family: var(--font-title); font-size: 1.2rem; color: #34d399; margin-bottom: 1.25rem; border-left: 4px solid #34d399; padding-left: 0.75rem;">
                <i class="fa-solid fa-user-check"></i> Current Checked-In Guests (Active Stays)
            </h3>
            
            @if($activeStays->isEmpty())
                <div class="text-center text-muted py-4" style="border: 1px dashed var(--glass-border); border-radius: var(--radius-sm);">
                    <i class="fa-solid fa-users-slash fa-2x mb-2 text-muted"></i>
                    <p style="font-size: 0.9rem;">No guests are currently checked in.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Guest Name</th>
                                <th>Room No.</th>
                                <th>Check-In Time</th>
                                <th>Expected Check-Out</th>
                                <th>Advance Paid</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeStays as $stay)
                                <tr>
                                    <td>
                                        <strong style="color: #fff; font-size: 0.95rem;">{{ $stay->guest->name }}</strong>
                                        <small class="text-muted" style="display: block; margin-top: 2px;">
                                            <i class="fa-solid fa-phone" style="font-size: 0.75rem;"></i> {{ $stay->guest->phone }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-success" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                                            Room {{ $stay->room->room_number }}
                                        </span>
                                        <small class="text-muted" style="display: block; margin-top: 3px;">{{ $stay->room->room_type }}</small>
                                    </td>
                                    <td>
                                        {{ $stay->check_in->format('d M Y') }}
                                        <small class="text-muted" style="display: block; margin-top: 2px;">{{ $stay->check_in->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        {{ $stay->expected_check_out ? $stay->expected_check_out->format('d M Y') : 'N/A' }}
                                        <small class="text-muted" style="display: block; margin-top: 2px;">
                                            {{ $stay->expected_check_out ? $stay->expected_check_out->format('h:i A') : '' }}
                                        </small>
                                    </td>
                                    <td>₹{{ number_format($stay->advance_payment, 2) }}</td>
                                    <td><span class="badge badge-success">Checked-In</span></td>
                                    <td style="text-align: right;">
                                        <div class="actions-group" style="justify-content: flex-end;">
                                            <a href="{{ route('bookings.edit', $stay->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="{{ route('checkout.form', $stay->id) }}" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-cash-register"></i> Check-out / Bill
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- SECTION 2: UPCOMING RESERVATIONS -->
        <div>
            <h3 style="font-family: var(--font-title); font-size: 1.2rem; color: #818cf8; margin-bottom: 1.25rem; border-left: 4px solid #818cf8; padding-left: 0.75rem;">
                <i class="fa-solid fa-calendar-days"></i> Upcoming Reservations (Advance Bookings)
            </h3>
            
            @if($upcomingReservations->isEmpty())
                <div class="text-center text-muted py-4" style="border: 1px dashed var(--glass-border); border-radius: var(--radius-sm);">
                    <i class="fa-solid fa-calendar-xmark fa-2x mb-2 text-muted"></i>
                    <p style="font-size: 0.9rem;">No upcoming advance reservations booked.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Guest Name</th>
                                <th>Room No.</th>
                                <th>Booked Check-In</th>
                                <th>Expected Check-Out</th>
                                <th>Advance Paid</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingReservations as $booking)
                                <tr>
                                    <td>
                                        <strong style="color: #fff; font-size: 0.95rem;">{{ $booking->guest->name }}</strong>
                                        <small class="text-muted" style="display: block; margin-top: 2px;">
                                            <i class="fa-solid fa-phone" style="font-size: 0.75rem;"></i> {{ $booking->guest->phone }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning" style="font-size: 0.8rem; padding: 0.3rem 0.6rem; color: #1e1b4b; background-color: #fde047;">
                                            Room {{ $booking->room->room_number }}
                                        </span>
                                        <small class="text-muted" style="display: block; margin-top: 3px;">{{ $booking->room->room_type }}</small>
                                    </td>
                                    <td>
                                        {{ $booking->check_in->format('d M Y') }}
                                        <small class="text-muted" style="display: block; margin-top: 2px;">{{ $booking->check_in->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        {{ $booking->expected_check_out ? $booking->expected_check_out->format('d M Y') : 'N/A' }}
                                        <small class="text-muted" style="display: block; margin-top: 2px;">
                                            {{ $booking->expected_check_out ? $booking->expected_check_out->format('h:i A') : '' }}
                                        </small>
                                    </td>
                                    <td>₹{{ number_format($booking->advance_payment, 2) }}</td>
                                    <td><span class="badge badge-warning">Reserved</span></td>
                                    <td style="text-align: right;">
                                        <div class="actions-group" style="justify-content: flex-end;">
                                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            @if(now()->startOfDay()->gte($booking->check_in->startOfDay()))
                                                <form action="{{ route('bookings.checkin', $booking->id) }}" method="POST" class="confirm-checkin-form" style="display: inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa-solid fa-key"></i> Check-In Now
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted" style="font-size: 0.8rem; font-style: italic; display: inline-block; padding: 0.35rem 0.5rem;" title="Cannot check in before the booking date.">
                                                    <i class="fa-solid fa-clock"></i> Opens on {{ $booking->check_in->format('d M') }}
                                                </span>
                                            @endif
                                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="confirm-cancel-form" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-circle-xmark"></i> Cancel
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    <!-- Filtered Modes: Single list table -->
    @else
        @if($bookings->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fa-solid fa-calendar-xmark fa-3x mb-4"></i>
                <p>No records found for the selected status.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Guest Name</th>
                            <th>Room No.</th>
                            <th>Check-In Time</th>
                            <th>Expected Check-Out</th>
                            <th>Advance Paid</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <strong style="color: #fff; font-size: 0.95rem;">{{ $booking->guest->name }}</strong>
                                    <small class="text-muted" style="display: block; margin-top: 2px;">
                                        <i class="fa-solid fa-phone" style="font-size: 0.75rem;"></i> {{ $booking->guest->phone }}
                                    </small>
                                </td>
                                <td>
                                    <span class="badge badge-info" style="font-size: 0.8rem; padding: 0.3rem 0.6rem;">
                                        Room {{ $booking->room->room_number }}
                                    </span>
                                    <small class="text-muted" style="display: block; margin-top: 3px;">{{ $booking->room->room_type }}</small>
                                </td>
                                <td>
                                    {{ $booking->check_in->format('d M Y') }}
                                    <small class="text-muted" style="display: block; margin-top: 2px;">{{ $booking->check_in->format('h:i A') }}</small>
                                </td>
                                <td>
                                    {{ $booking->expected_check_out ? $booking->expected_check_out->format('d M Y') : 'N/A' }}
                                    <small class="text-muted" style="display: block; margin-top: 2px;">
                                        {{ $booking->expected_check_out ? $booking->expected_check_out->format('h:i A') : '' }}
                                    </small>
                                </td>
                                <td>₹{{ number_format($booking->advance_payment, 2) }}</td>
                                <td>
                                    @if($booking->status === 'Booked')
                                        <span class="badge badge-warning">Reserved</span>
                                    @elseif($booking->status === 'Active')
                                        <span class="badge badge-success">Checked-In</span>
                                    @elseif($booking->status === 'Completed')
                                        <span class="badge badge-info">Completed</span>
                                    @else
                                        <span class="badge badge-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div class="actions-group" style="justify-content: flex-end;">
                                        @if($booking->status === 'Booked')
                                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            @if(now()->startOfDay()->gte($booking->check_in->startOfDay()))
                                                <form action="{{ route('bookings.checkin', $booking->id) }}" method="POST" class="confirm-checkin-form" style="display: inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="fa-solid fa-key"></i> Check-In Now
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted" style="font-size: 0.8rem; font-style: italic; display: inline-block; padding: 0.35rem 0.5rem;" title="Cannot check in before the booking date.">
                                                    <i class="fa-solid fa-clock"></i> Opens on {{ $booking->check_in->format('d M') }}
                                                </span>
                                            @endif
                                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="confirm-cancel-form" style="display: inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa-solid fa-circle-xmark"></i> Cancel
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'Active')
                                            <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </a>
                                            <a href="{{ route('checkout.form', $booking->id) }}" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-cash-register"></i> Check-out / Bill
                                            </a>
                                        @elseif($booking->status === 'Completed')
                                            <a href="{{ route('checkout.invoice', $booking->id) }}" class="btn btn-secondary btn-sm">
                                                <i class="fa-solid fa-print"></i> Invoice
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 1.5rem;">
                {{ $bookings->appends(request()->input())->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Check-in Now Confirmation (SweetAlert2)
    $('.confirm-checkin-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Confirm Check-In',
            text: 'Do you want to check in this guest now?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981', // success theme green
            cancelButtonColor: '#6b7280', // muted gray
            confirmButtonText: 'Yes, Check-In!',
            cancelButtonText: 'No, cancel',
            background: '#121826', // dark matching theme
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Cancel Booking Confirmation (SweetAlert2)
    $('.confirm-cancel-form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        Swal.fire({
            title: 'Cancel Reservation',
            text: 'Are you sure you want to cancel this booking?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f43f5e', // danger theme red
            cancelButtonColor: '#6b7280', // muted gray
            confirmButtonText: 'Yes, Cancel it!',
            cancelButtonText: 'No, keep it',
            background: '#121826', // dark matching theme
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection
