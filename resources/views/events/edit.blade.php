@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">Edit Event</h1>
            </div>
            <div class="mt-4 flex md:mt-0">
                <a href="{{ route('events.show', $event) }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Back to Event
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <event-form
                    :initial-data='@json($event)'
                    submit-url="{{ route('events.update', $event) }}"
                    submit-method="PUT"
                    :is-edit="true"
                    @success="window.location.href = $event.redirect_url"
                    @cancel="window.location.href = '{{ route('events.show', $event) }}'"
                ></event-form>
            </div>
        </div>
    </div>
</div>
@endsection
