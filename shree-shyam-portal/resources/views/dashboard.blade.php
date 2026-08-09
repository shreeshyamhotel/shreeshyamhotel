@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')
@section('header_subtitle', 'Overview of Shree Shyam Hotel & Restaurant occupancy and guests')

@section('content')
<!-- Stats Grid -->
<div class="stats-grid">
    <div class="glass-panel stat-card">
        <div class="stat-icon primary">
            <i class="fa-solid fa-door-closed"></i>
        </div>
        <div class="stat-info">
            <h4>Total Rooms</h4>
            <div class="stat-value">{{ $stats['total_rooms'] }}</div>
        </div>
    </div>
    
    <div class="glass-panel stat-card">
        <div class="stat-icon success">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="stat-info">
            <h4>Available</h4>
            <div class="stat-value">{{ $stats['available_rooms'] }}</div>
        </div>
    </div>
    
    <div class="glass-panel stat-card">
        <div class="stat-icon danger">
            <i class="fa-solid fa-user-tag"></i>
        </div>
        <div class="stat-info">
            <h4>Occupied</h4>
            <div class="stat-value">{{ $stats['occupied_rooms'] }}</div>
        </div>
    </div>
    
    <div class="glass-panel stat-card">
        <div class="stat-icon warning">
            <i class="fa-solid fa-broom"></i>
        </div>
        <div class="stat-info">
            <h4>Service/Cleaning</h4>
            <div class="stat-value">{{ $stats['maintenance_rooms'] }}</div>
        </div>
    </div>
    
    <div class="glass-panel stat-card">
        <div class="stat-icon info">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-info">
            <h4>Active Guests</h4>
            <div class="stat-value">{{ $stats['active_guests'] }}</div>
        </div>
    </div>

    <div class="glass-panel stat-card" onclick="window.location='{{ route('bookings.index') }}'" style="cursor: pointer;">
        <div class="stat-icon" style="background-color: rgba(79, 70, 229, 0.1); color: #818cf8; border: 1px solid rgba(79, 70, 229, 0.25);">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <h4>Bookings</h4>
            <div class="stat-value">{{ $stats['upcoming_bookings'] }}</div>
        </div>
    </div>
</div>

<!-- Dashboard Columns -->
<div class="dashboard-sections">
    <!-- Active Check-Ins (Left Column) -->
    <div class="glass-panel section-card">
        <div class="section-header">
            <h2><i class="fa-solid fa-user-check"></i> Active Checked-In Guests</h2>
            <span class="badge badge-info">{{ $activeStays->count() }} Guests Stay</span>
        </div>
        
        @if($activeStays->isEmpty())
            <div class="text-center text-muted py-5 my-4">
                <i class="fa-solid fa-bed fa-3x mb-4"></i>
                <p>No active guest stays at the moment.</p>
                <a href="{{ route('checkin.form') }}" class="btn btn-primary mt-4">
                    <i class="fa-solid fa-plus"></i> New Guest Check-In
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Guest Name</th>
                            <th>Phone</th>
                            <th>Check-in Date & Time</th>
                            <th>Rate (Night)</th>
                            <th>Advance</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeStays as $stay)
                            <tr>
                                <td>
                                    <span class="badge badge-success" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                                        Room {{ $stay->room->room_number }}
                                    </span>
                                    <small style="display:block; margin-top:3px;" class="text-muted">{{ $stay->room->room_type }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('guests.show', $stay->guest_id) }}" style="color: #fff; font-weight: 600; text-decoration: none; hover: underline;">
                                        {{ $stay->guest->name }}
                                    </a>
                                </td>
                                <td>{{ $stay->guest->phone }}</td>
                                <td>
                                    {{ $stay->check_in->format('d M Y') }}
                                    <small class="text-muted" style="display:block;">{{ $stay->check_in->format('h:i A') }}</small>
                                </td>
                                <td>₹{{ number_format($stay->price_per_night, 2) }}</td>
                                <td>₹{{ number_format($stay->advance_payment, 2) }}</td>
                                <td style="text-align: right;">
                                    <div class="actions-group" style="justify-content: flex-end;">
                                        <a href="{{ route('checkout.form', $stay->id) }}" class="btn btn-danger btn-sm" title="Add charges & Check-out">
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

    <!-- Quick Operations & Recent Activity (Right Column) -->
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <!-- Quick Operations Panel -->
        <div class="glass-panel">
            <div class="section-header">
                <h2><i class="fa-solid fa-bolt"></i> Quick Actions</h2>
            </div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="{{ route('checkin.form') }}" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-user-plus"></i> New Check-In Entry
                </a>
                <a href="{{ route('bookings.create') }}" class="btn btn-secondary" style="width: 100%; border-color: rgba(79, 70, 229, 0.4); background-color: rgba(79, 70, 229, 0.05); color: #818cf8;">
                    <i class="fa-solid fa-calendar-plus"></i> New Advance Booking
                </a>
                <a href="{{ route('rooms.create') }}" class="btn btn-secondary" style="width: 100%;">
                    <i class="fa-solid fa-plus"></i> Add New Room
                </a>
                <a href="{{ route('guests.index') }}" class="btn btn-secondary" style="width: 100%;">
                    <i class="fa-solid fa-search"></i> Search Guest History
                </a>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="glass-panel" style="flex-grow: 1;">
            <div class="section-header">
                <h2><i class="fa-solid fa-history"></i> Recent Logs</h2>
            </div>
            @if($recentRecords->isEmpty())
                <p class="text-muted text-center py-4">No recent activity logs.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($recentRecords as $record)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <div>
                                <h4 style="font-size: 0.9rem; font-weight: 600; color: #fff;">{{ $record->guest->name }}</h4>
                                <small class="text-muted">
                                    Room {{ $record->room->room_number }} | 
                                    @if($record->status === 'Active')
                                        Checked-in
                                    @else
                                        Checked-out
                                    @endif
                                </small>
                            </div>
                            <div>
                                @if($record->status === 'Active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Completed</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
