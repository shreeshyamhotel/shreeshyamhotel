@extends('layouts.app')

@section('title', 'Guest Directory')
@section('header_title', 'Guest Directory')
@section('header_subtitle', 'Lookup past and returning guest records and stay history')

@section('content')
<div class="glass-panel">
    <!-- Search Bar & Title Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1.5rem; flex-wrap: wrap;">
        <h2 style="font-family: var(--font-title); font-size: 1.25rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fa-solid fa-address-book text-muted"></i> Guest List
        </h2>
        
        <form action="{{ route('guests.index') }}" method="GET" style="display: flex; gap: 0.5rem; width: 100%; max-width: 450px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name, phone, or ID proof..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('guests.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    @if($guests->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="fa-solid fa-users-slash fa-3x mb-4"></i>
            <p>No guest records found.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Phone Number</th>
                        <th>Email Address</th>
                        <th>ID Document Type</th>
                        <th>ID Document Number</th>
                        <th>City & State</th>
                        <th style="text-align: right;">Profile History</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guests as $guest)
                        <tr>
                            <td>
                                <strong style="color: #fff; font-size: 0.95rem;">{{ $guest->name }}</strong>
                            </td>
                            <td>{{ $guest->phone }}</td>
                            <td>{{ $guest->email ?? 'N/A' }}</td>
                            <td>{{ $guest->id_type }}</td>
                            <td><code>{{ $guest->id_number }}</code></td>
                            <td>
                                @if($guest->city || $guest->state)
                                    {{ $guest->city }}{{ $guest->city && $guest->state ? ', ' : '' }}{{ $guest->state }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('guests.show', $guest->id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fa-solid fa-eye"></i> View Profile
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $guests->appends(request()->input())->links() }}
        </div>
    @endif
</div>
@endsection
