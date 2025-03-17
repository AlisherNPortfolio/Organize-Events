@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Events</h1>
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Create Event
            </a>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Active Events</h2>
                <event-list :initial-events='@json($activeEvents)' :autoload="false"></event-list>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Events</h2>
                <event-list :initial-events='@json($upcomingEvents)' :autoload="false"></event-list>
            </div>
        </div>
    </div>
</div>
@endsection
