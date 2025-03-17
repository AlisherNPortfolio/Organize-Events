@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">Manage Events</h1>
            </div>
            <div class="mt-4 flex md:mt-0">
                <a href="{{ route('admin.dashboard') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <div class="flex space-x-2">
                <a href="{{ route('admin.events.index') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">
                    All Events
                </a>
                <a href="{{ route('admin.events.index', ['status' => 'active']) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md {{ request('status') === 'active' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">
                    Active
                </a>
                <a href="{{ route('admin.events.index', ['status' => 'pending']) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md {{ request('status') === 'pending' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">
                    Pending
                </a>
                <a href="{{ route('admin.events.index', ['status' => 'completed']) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md {{ request('status') === 'completed' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">
                    Completed
                </a>
                <a href="{{ route('admin.events.index', ['status' => 'cancelled']) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md {{ request('status') === 'cancelled' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border-gray-300' }}">
                    Cancelled
                </a>
            </div>
            <div>
                <form method="GET" action="{{ route('admin.events.index') }}" class="flex space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..." class="block w-64 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Search
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Event
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Creator
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Participants
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($events as $event)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $event->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ Str::limit($event->description, 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $event->creator->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $event->creator->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $event->event_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ date('g:i A', strtotime($event->start_time)) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $event->status === 'active' ? 'bg-green-100 text-green-800' : ($event->status === 'completed' ? 'bg-blue-100 text-blue-800' : ($event->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $event->participants->count() }} / {{ $event->max_participants }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.events.show', $event->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        View
                                    </a>
                                    <form action="{{ route('admin.events.update-status', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="text-sm border-gray-300 rounded-md">
                                            <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ $event->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $event->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            <option value="pending" {{ $event->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        </select>
                                    </form>
                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    No events found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($events->hasPages())
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                    {{ $events->links() }}
                </div>
            @endif
        </div>

        <div class="mt-8 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-lg leading-6 font-medium text-gray-900">Event Statistics</h2>
                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-4">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Events
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $totalEvents }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Active Events
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $activeEvents }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Completed Events
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $completedEvents }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Cancelled Events
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                    {{ $cancelledEvents }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
