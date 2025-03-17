<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventImageController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Home Routes
Route::get('/', function () {
    return view('home', [
        'upcomingEvents' => App\Models\Event::where('event_date', '>=', now()->toDateString())
            ->where('status', 'active')
            ->orderBy('event_date')
            ->take(6)
            ->get()
    ]);
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth.custom')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // User Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

    // Events Routes
    Route::middleware('check.fine')->group(function () {
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::post('/events/{event}/vote', [EventController::class, 'vote'])
            ->middleware('permission:events.participate')
            ->name('events.vote');
        Route::delete('/events/{event}/vote', [EventController::class, 'cancelVote'])
            ->middleware('permission:events.participate')
            ->name('events.cancel-vote');
    });

    // Creator/Admin Only Routes
    Route::middleware('permission:events.create')->group(function () {
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
    });

    // Routes for viewing an event (everyone)
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    // Creator Routes - Own Events Management
    Route::middleware('role:admin,creator')->group(function () {
        // My Events
        Route::get('/my-events', [EventController::class, 'myEvents'])->name('events.my-events');

        // Event Edit/Update (own events only or admin)
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
        Route::put('/events/{event}/complete', [EventController::class, 'complete'])->name('events.complete');

        // Participants Management
        Route::get('/events/{event}/participants', [ParticipantController::class, 'index'])->name('participants.index');
        Route::put('/events/{event}/participants/{participant}', [ParticipantController::class, 'updateStatus']);
        Route::get('/my-event-participants', [ParticipantController::class, 'myEventParticipants'])->name('participants.my-event-participants');
    });

    // Event Images Routes
    Route::post('/events/{event}/images', [EventImageController::class, 'store'])
        ->middleware('permission:images.upload');

    // Admin Routes
    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::get('/users/{user}/apply-fine', [AdminUserController::class, 'showApplyFineForm'])->name('users.apply-fine');
        Route::post('/users/{user}/apply-fine', [AdminUserController::class, 'applyFine']);
        Route::put('/users/{user}/remove-fine', [AdminUserController::class, 'removeFine'])->name('users.remove-fine');

        // Event Management
        Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
        Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('events.show');
        Route::put('/events/{event}/status', [AdminEventController::class, 'updateStatus'])->name('events.update-status');
        Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    });
});

// API Routes for Vue Components
Route::middleware('auth.custom')->prefix('api')->group(function () {
    Route::get('/events', function () {
        return App\Models\Event::where('status', 'active')
            ->where('event_date', '>=', now()->toDateString())
            ->orderBy('event_date')
            ->get();
    });

    Route::get('/events/{event}/participants', function ($eventId) {
        $event = App\Models\Event::findOrFail($eventId);

        // Check if current user is admin, the event creator, or has proper permission
        if (auth()->user()->isAdmin() ||
            $event->user_id === auth()->id() ||
            auth()->user()->hasPermission('participants.view-any')) {
            return $event->participants()->with('user')->get();
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    });

    Route::get('/events/{event}/images', function ($eventId) {
        $event = App\Models\Event::findOrFail($eventId);
        return $event->images()->with('user')->get();
    });

    Route::delete('/events/{event}/images/{image}', function ($eventId, $imageId) {
        $event = App\Models\Event::findOrFail($eventId);
        $image = $event->images()->findOrFail($imageId);

        // Check if current user is the uploader, the event creator, or admin
        if ($image->user_id === auth()->id() ||
            $event->user_id === auth()->id() ||
            auth()->user()->isAdmin()) {

            // Delete image from storage
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();

            return response()->json(['message' => 'Image deleted successfully']);
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    });
});
