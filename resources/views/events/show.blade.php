@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
            </div>
            <div class="mt-4 mb-4 flex md:mt-0">
                <a href="{{ route('events.index') }}" class="back-to ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
    </div>
</div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 left-box">
            <div class="card single_post">
                <div class="body">
                    @if ($event->image_path)
                    <div class="img-post">
                        <img class="d-block img-fluid" src="/storage/{{ $event->image_path }}"
                            alt="First slide">
                    </div>
                    @endif
                    <div>
                        <b>Organizer: </b>
                        <span>{{ $event->creator->name }}</span>
                    </div>
                    <div>
                        <b>Status: </b>
                        <span class="
                        px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $event->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $event->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $event->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $event->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                    ">
                    {{ ucfirst($event->status) }}
                    </span>
                    </div>
                    <div>
                        <b>Date & Time: </b>
                        <span>{{ $event->event_date->format('F j, Y') }} at {{ date('g:i A', strtotime($event->start_time)) }}
                            @if($event->end_time)
                                - {{ date('g:i A', strtotime($event->end_time)) }}
                            @endif</span>
                    </div>
                    <div>
                        <b>Location: </b>
                        <span>{{ $event->address }}</span>
                    </div>
                    <div>
                        <b>Participants: </b>
                        <span>{{ $event->participants->count() }} / {{ $event->max_participants }} (Min: {{ $event->min_participants }})</span>
                    </div>
                    <div>
                        <b>Voting Expiry: </b>
                        <span>{{ $event->voting_expiry_time->format('F j, Y \a\t g:i A') }}</span>
                        @if($event->isVotingOpen())
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Open
                            </span>
                        @else
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Closed
                            </span>
                        @endif
                    </div>
                    <div class="mt-4">
                        <p>{{ $event->description }}</p>
                    </div>
                </div>
            </div>
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

            <div class="card">
                <div class="header">
                    <h2>Event images</small>
                    </h2>
                </div>
                <div class="body mt-0 pt-0">
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
        {{-- <div class="col-lg-4 col-md-12 right-box">
            <div class="card">
                <div class="body search">
                    <div class="input-group m-b-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Search...">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="header">
                    <h2>Categories Clouds</h2>
                </div>
                <div class="body widget">
                    <ul class="list-unstyled categories-clouds m-b-0">
                        <li><a href="javascript:void(0);">eCommerce</a></li>
                        <li><a href="javascript:void(0);">Microsoft Technologies</a></li>
                        <li><a href="javascript:void(0);">Creative UX</a></li>
                        <li><a href="javascript:void(0);">Wordpress</a></li>
                        <li><a href="javascript:void(0);">Angular JS</a></li>
                        <li><a href="javascript:void(0);">Enterprise Mobility</a></li>
                        <li><a href="javascript:void(0);">Website Design</a></li>
                        <li><a href="javascript:void(0);">HTML5</a></li>
                        <li><a href="javascript:void(0);">Infographics</a></li>
                        <li><a href="javascript:void(0);">Wordpress Development</a></li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="header">
                    <h2>Popular Posts</h2>
                </div>
                <div class="body widget popular-post">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="single_post">
                                <p class="m-b-0">Apple Introduces Search Ads Basic</p>
                                <span>jun 22, 2018</span>
                                <div class="img-post">
                                    <img src="https://www.bootdey.com/image/280x280/87CEFA/000000" alt="Awesome Image">
                                </div>
                            </div>
                            <div class="single_post">
                                <p class="m-b-0">new rules, more cars, more races</p>
                                <span>jun 8, 2018</span>
                                <div class="img-post">
                                    <img src="https://www.bootdey.com/image/280x280/87CEFA/000000" alt="Awesome Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="header">
                    <h2>Instagram Post</h2>
                </div>
                <div class="body widget">
                    <ul class="list-unstyled instagram-plugin m-b-0">
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                        <li><a href="javascript:void(0);"><img src="https://www.bootdey.com/image/100x100/87CEFA/000000"
                                    alt="image description"></a></li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="header">
                    <h2>Email Newsletter <small>Get our products/news earlier than others, let’s get in touch.</small></h2>
                </div>
                <div class="body widget newsletter">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Enter Email">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="icon-paper-plane"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
@push('styles')
    <link href="/assets/event-details.css" rel="stylesheet" />
@endpush
