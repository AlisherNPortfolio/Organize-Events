@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
            </div>
            <div class="mt-4 flex md:mt-0">
                <a href="{{ route('events.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Back to Events
                </a>
                @if($isCreator)
                    <a href="{{ route('events.edit', $event) }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Edit Event
                    </a>
                    @if($event->status === 'active')
                        <form action="{{ route('events.complete', $event) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                Mark Completed
                            </button>
                        </form>
                    @endif
                @else
                    @if($event->isVotingOpen() && !$userParticipation && !$currentUser->isFined())
                        <form action="{{ route('events.vote', $event) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                Join Event
                            </button>
                        </form>
                    @elseif($userParticipation && $event->isVotingOpen())
                        <form action="{{ route('events.cancel-vote', $event) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                Cancel Participation
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Event Details
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Information about the event.
                </p>
            </div>
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Status
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <span class="
                                px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $event->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $event->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $event->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $event->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ ucfirst($event->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Description
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->description }}
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Organizer
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->creator->name }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Date & Time
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->event_date->format('F j, Y') }} at {{ date('g:i A', strtotime($event->start_time)) }}
                            @if($event->end_time)
                                - {{ date('g:i A', strtotime($event->end_time)) }}
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Location
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->address }}
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Participants
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->participants->count() }} / {{ $event->max_participants }} (Min: {{ $event->min_participants }})
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Voting Expiry
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $event->voting_expiry_time->format('F j, Y \a\t g:i A') }}
                            @if($event->isVotingOpen())
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Open
                                </span>
                            @else
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Closed
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Participants</h2>
                <participants-list
                    :initial-participants='@json($event->participants->load("user"))'
                    :event-id="{{ $event->id }}"
                    :is-event-creator="{{ json_encode($isCreator) }}"
                    :event-active="{{ json_encode($event->status === 'active') }}"
                ></participants-list>

                @if($isCreator)
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('participants.index', $event) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Manage Participants
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Images</h2>
                <event-images
                    :event-id="{{ $event->id }}"
                    :initial-images='@json($eventImages)'
                    :can-upload="{{ json_encode($canUploadImages) }}"
                    :current-user-id="{{ json_encode(auth()->id()) }}"
                    :is-event-creator="{{ json_encode($isCreator) }}"
                ></event-images>
            </div>
        </div>
    </div>
</div>
@endsection
