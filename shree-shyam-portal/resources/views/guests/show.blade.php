@extends('layouts.app')

@section('title', 'Guest Profile')
@section('header_title', 'Guest Profile')
@section('header_subtitle', 'Detailed dossier of ' . $guest->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Top back button -->
    <div>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to Directory
        </a>
    </div>

    <div class="checkout-grid">
        <!-- Left panel: Guest Details Cards -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Profile Info Card -->
            <div class="glass-panel">
                <div class="section-header">
                    <h2><i class="fa-solid fa-user-circle"></i> Personal Dossier</h2>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">Full Name</small>
                        <div style="font-size: 1.15rem; font-weight: 600; color: #fff;">{{ $guest->name }}</div>
                    </div>

                    <div>
                        <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">Phone Number</small>
                        <div style="font-size: 1.05rem; font-weight: 600; color: #fff;">{{ $guest->phone }}</div>
                    </div>

                    <div>
                        <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">Email Address</small>
                        <div>{{ $guest->email ?? 'N/A' }}</div>
                    </div>

                    <div>
                        <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">Residential Address</small>
                        <div>
                            @if($guest->address || $guest->city || $guest->state)
                                {{ $guest->address }}<br>
                                {{ $guest->city }}, {{ $guest->state }}
                            @else
                                <span class="text-muted">No address stored</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ID Verification Card -->
            <div class="glass-panel">
                <div class="section-header">
                    <h2><i class="fa-solid fa-id-card"></i> Identity Verification</h2>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">ID Proof Type</small>
                        <div style="font-weight: 600; color: #fff;">{{ $guest->id_type }}</div>
                    </div>

                    <div>
                        <small class="text-muted" style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">ID Number Reference</small>
                        <div><code>{{ $guest->id_number }}</code></div>
                    </div>

                    @if($guest->id_proof_path)
                        <div style="margin-top: 0.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                            @if(is_array($guest->id_proof_path))
                                @foreach($guest->id_proof_path as $index => $path)
                                    <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-secondary" style="width: 100%;">
                                        <i class="fa-solid fa-file-invoice"></i> View Uploaded ID Document {{ $index + 1 }}
                                    </a>
                                @endforeach
                            @else
                                <a href="{{ asset('storage/' . $guest->id_proof_path) }}" target="_blank" class="btn btn-secondary" style="width: 100%;">
                                    <i class="fa-solid fa-file-invoice"></i> View Uploaded ID Document
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning" style="margin-bottom: 0; padding: 0.75rem 1rem;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div class="alert-content">No ID attachment uploaded.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right panel: Stay History list -->
        <div class="glass-panel">
            <div class="section-header">
                <h2><i class="fa-solid fa-history"></i> Stay History Ledger</h2>
                <span class="badge badge-primary">{{ $guest->stayRecords->count() }} Stays Total</span>
            </div>

            @if($guest->stayRecords->isEmpty())
                <p class="text-muted py-4 text-center">No stay history recorded for this guest.</p>
            @else
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @foreach($guest->stayRecords as $record)
                        <div class="glass-panel" style="padding: 1.25rem; background-color: rgba(255,255,255,0.01);">
                            <div class="d-flex justify-between align-center mb-4">
                                <div>
                                    <span class="badge badge-success">Room {{ $record->room->room_number }}</span>
                                    <span class="text-muted" style="font-size: 0.85rem; margin-left: 0.5rem;">{{ $record->room->room_type }}</span>
                                </div>
                                <div>
                                    @if($record->status === 'Active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Completed</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.85rem;">
                                <div>
                                    <span class="text-muted">Check-In:</span><br>
                                    <strong>{{ $record->check_in->format('d M Y, h:i A') }}</strong>
                                </div>
                                <div>
                                    <span class="text-muted">Check-Out:</span><br>
                                    <strong>
                                        @if($record->actual_check_out)
                                            {{ $record->actual_check_out->format('d M Y, h:i A') }}
                                        @else
                                            <span class="text-muted">Still checked-in</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>

                            <div style="margin-top: 1rem; border-top: 1px dashed var(--glass-border); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                                <div>
                                    <span class="text-muted">Room Rent:</span> ₹{{ number_format($record->price_per_night, 2) }}
                                </div>
                                <div>
                                    @if($record->status === 'Completed')
                                        <a href="{{ route('checkout.invoice', $record->id) }}" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                            <i class="fa-solid fa-print"></i> View Invoice
                                        </a>
                                    @else
                                        <a href="{{ route('checkout.form', $record->id) }}" class="btn btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                            <i class="fa-solid fa-cash-register"></i> Check-out
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
