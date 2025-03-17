@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Admin Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Users
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $stats['total_users'] }}
                        </dd>
                    </dl>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.users.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            View all users
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total Events
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $stats['total_events'] }}
                        </dd>
                    </dl>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.events.index') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            View all events
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Active Fines
                        </dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900">
                            {{ $stats['active_fines'] }}
                        </dd>
                    </dl>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6">
                    <div class="text-sm">
                        <a href="{{ route('admin.users.index') }}?fined=1" class="font-medium text-indigo-600 hover:text-indigo-500">
                            View fined users
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Latest Users
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <ul>
                        @foreach($latestUsers as $user)
                            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            @if($user->avatar)
                                                <img class="h-10 w-10 rounded-full" src="{{ '/storage/' . $user->avatar }}" alt="{{ $user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-600 text-sm font-medium">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'creator' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Latest Events
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <ul>
                        @foreach($latestEvents as $event)
                            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $event->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            By {{ $event->creator->name }} on {{ $event->event_date->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $event->status === 'active' ? 'bg-green-100 text-green-800' : ($event->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
