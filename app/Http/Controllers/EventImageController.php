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

        return $this->imageUploadService->uploadEventImage(
            $eventId,
            Auth::id(),
            $request->file('image')
        );
    }
}
