<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\EventImageStoreRequest;
use App\Services\ImageUploadService;
use Illuminate\Support\Facades\Auth;

class EventImageController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    public static function middleware()
    {
        return [
            'auth',
        ];
    }

    public function store(EventImageStoreRequest $request, $eventId)
    {
        $request->validated();

        $result = $this->imageUploadService->uploadEventImage(
            $eventId,
            Auth::id(),
            $request->file('image')
        );

        if ($result['status']) {
            return redirect()->route('events.show', $eventId)
                ->with('success', $result['message']);
        }

        return redirect()->route('events.show', $eventId)
            ->with('error', $result['message']);
    }
}
