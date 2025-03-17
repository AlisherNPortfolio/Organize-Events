@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-12">
            <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Event Organizer</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Organize events with ease
            </p>
            <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                Create events, invite participants, and manage attendance all in one place.
            </p>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Upcoming Events
                </h3>
                <div class="mt-5">
                    <event-list :initial-events='@json($upcomingEvents)' :autoload="false"></event-list>
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        View all events
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Create Events</h3>
                        <p class="mt-3 text-base text-gray-500">
                            Create different types of events, set dates, locations, and participant limits.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Invite Participants</h3>
                        <p class="mt-3 text-base text-gray-500">
                            Share your events and allow participants to vote for attending.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col rounded-lg shadow-lg overflow-hidden">
                <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                    <div class="flex-1">
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Track Attendance</h3>
                        <p class="mt-3 text-base text-gray-500">
                            Keep track of who attends events and manage no-shows with fines.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
