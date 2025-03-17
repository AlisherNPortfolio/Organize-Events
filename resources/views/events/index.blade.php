@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Events</h1>
        @if(Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'creator'))
        <a href="{{ route('events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Event
        </a>
        @endif
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Active Events</h2>
        </div>
        <div class="card-body">
            <event-list :initial-events='@json($activeEvents->items())' :autoload="false"></event-list>

            {{ $activeEvents->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white">
            <h2 class="h5 mb-0">Upcoming Events</h2>
        </div>
        <div class="card-body">
            <event-list :initial-events='@json($upcomingEvents->items())' :autoload="false"></event-list>

            {{ $upcomingEvents->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection