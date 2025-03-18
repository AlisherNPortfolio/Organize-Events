@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">Manage Participants</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Event: Test
                </p>
            </div>
            <div class="mt-4 flex md:mt-0">
                <a href="/" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Back to Event
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                @if (count($eventParticipants))
                {{-- <participants-list
                    :initial-participants='@json($eventParticipants)'
                    :event-id="1"
                    :is-event-creator="true"
                    :event-active="true"
                ></participants-list> --}}
                <event-list
                    :initial-participants='@json($eventParticipants)'
                    :event-id="1"
                    :is-event-creator="true"
                    :event-active="true"
                ></event-list>
                @else
                <span class="text-gray-500">No participants found.</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
