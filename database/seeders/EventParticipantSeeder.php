<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $events = Event::where('status', 'active')->orWhere('status', 'completed')->get();
        $users = User::where('role', 'user')->where('status', 'active')->get();

        foreach ($events as $event) {
            // For each event, randomly select between min_participants and max_participants users
            $participantsCount = rand(
                $event->min_participants,
                min($event->max_participants, count($users))
            );

            $randomUsers = $users->random($participantsCount);

            foreach ($randomUsers as $user) {
                // Skip if user is the event creator
                if ($user->id === $event->user_id) {
                    continue;
                }

                // Create participant record
                $status = 'voted';

                // If event is completed, randomly mark as attended or no_show
                if ($event->status === 'completed') {
                    $status = rand(0, 5) > 0 ? 'attended' : 'no_show'; // 5/6 chance to attend
                }

                EventParticipant::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'status' => $status,
                ]);
            }
        }
    }
}
