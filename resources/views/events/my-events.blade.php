@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">My Events</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (count($events) > 0)
                        <event-list :initial-events='@json($events)'></event-list>
                    @else
                        <span class="text-gray-500">No events found.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
