@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">Apply Fine to User</h1>
                <p class="mt-1 text-sm text-gray-500">User: {{ $user->name }}</p>
            </div>
            <div class="mt-4 flex md:mt-0">
                <a href="{{ route('admin.users.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Back to Users
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('admin.users.apply-fine', $user->id) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Fine</label>
                        <div class="mt-1">
                            <input id="reason" name="reason" type="text" value="{{ old('reason') }}" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        @error('reason')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700">Duration (days)</label>
                        <div class="mt-1">
                            <input id="duration_days" name="duration_days" type="number" min="1" max="90" value="{{ old('duration_days', 7) }}" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        @error('duration_days')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('admin.users.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Apply Fine
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
