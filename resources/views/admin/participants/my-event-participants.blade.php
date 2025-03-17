@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">My Event Participants</h1>

        @if(count($eventParticipants) > 0)
            @foreach($eventParticipants as $eventId => $data)
                <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                    <div class="px-4 py-5 sm:px-6 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $data['event']->name }}</h2>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $data['event']->status === 'active' ? 'bg-green-100 text-green-800' : ($data['event']->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($data['event']->status) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $data['event']->event_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-sm text-gray-500">Total participants: {{ $data['total'] }}</span>
                            <span class="text-sm text-gray-500 ml-4">Attended: {{ $data['attended'] }}</span>
                            <span class="text-sm text-gray-500 ml-4">No-show: {{ $data['no_show'] }}</span>
                        </div>
                    </div>

                    @if(count($data['participants']) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Joined On
                                        </th>
                                        @if($data['event']->status === 'active')
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($data['participants'] as $participant)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($participant->user->avatar)
                                                            <img class="h-10 w-10 rounded-full" src="{{ '/storage/' . $participant->user->avatar }}" alt="{{ $participant->user->name }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                                <span class="text-gray-600 text-sm font-medium">
                                                                    {{ substr($participant->user->name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $participant->user->name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">
                                                    {{ $participant->user->email }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $participant->status === 'attended' ? 'bg-green-100 text-green-800' : ($participant->status === 'no_show' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($participant->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $participant->created_at->format('M d, Y g:i A') }}
                                            </td>
                                            @if($data['event']->status === 'active')
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @if($participant->status === 'voted' || $participant->status === 'confirmed')
                                                        <form action="{{ route('participants.update-status', [$eventId, $participant->id]) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="attended">
                                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                                                Mark as Attended
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('participants.update-status', [$eventId, $participant->id]) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status" value="no_show">
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure? This will apply a fine to the user.')">
                                                                Mark as No-Show
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400">Already processed</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="px-4 py-5 sm:p-6">
                            <p class="text-gray-500">No participants for this event.</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <p class="text-gray-500">You don't have any events with participants yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
