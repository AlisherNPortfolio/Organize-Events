<?php

namespace App\Http\Controllers;

use App\Facades\EventFacade;
use Illuminate\Http\Request;

class EventImageController extends Controller
{
    public function __construct(protected EventFacade $eventFacade)
    {
    }

    public static function middleware()
    {
        return [
            'auth',
        ];
    }

    public function store(Request $request, $eventId)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $result = $this->eventFacade->uploadEventImage(
            $eventId,
            auth()->id(),
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
