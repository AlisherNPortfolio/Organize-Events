<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testUser = User::where('email', 'test@example.com')->first();

        if (!$testUser) {
            return;
        }

        // Create a football event
        Event::create([
            'user_id' => $testUser->id,
            'name' => 'Weekend Football Match',
            'description' => 'Join us for a friendly football match at the city park. All skill levels welcome!',
            'min_participants' => 6,
            'max_participants' => 22,
            'event_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'address' => 'City Park Football Field, Main Street',
            'voting_expiry_time' => Carbon::now()->addDays(8),
            'status' => 'active',
        ]);

        // Create a coffee meetup
        Event::create([
            'user_id' => $testUser->id,
            'name' => 'Coffee & Code Meetup',
            'description' => 'Let\'s meet up at Starbucks to discuss coding projects and help each other out!',
            'min_participants' => 2,
            'max_participants' => 10,
            'event_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'start_time' => '18:30:00',
            'end_time' => '20:30:00',
            'address' => 'Starbucks, Downtown Square',
            'voting_expiry_time' => Carbon::now()->addDays(3),
            'status' => 'active',
        ]);

        // Create a hiking trip
        Event::create([
            'user_id' => $testUser->id,
            'name' => 'Weekend Hiking Trip',
            'description' => 'A day trip to the mountains. Moderate difficulty, bring your own water and lunch!',
            'min_participants' => 3,
            'max_participants' => 15,
            'event_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '18:00:00',
            'address' => 'Mountain Trailhead Parking Lot',
            'voting_expiry_time' => Carbon::now()->addDays(12),
            'status' => 'active',
        ]);

        // Create a completed event
        Event::create([
            'user_id' => $testUser->id,
            'name' => 'Book Club Meeting',
            'description' => 'We\'ll be discussing "The Great Gatsby". Read it before coming!',
            'min_participants' => 3,
            'max_participants' => 12,
            'event_date' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'start_time' => '19:00:00',
            'end_time' => '21:00:00',
            'address' => 'City Library, Meeting Room 3',
            'voting_expiry_time' => Carbon::now()->subDays(7),
            'status' => 'completed',
        ]);

        // Create a cancelled event
        Event::create([
            'user_id' => $testUser->id,
            'name' => 'Basketball Game',
            'description' => 'Casual basketball game at the community center.',
            'min_participants' => 6,
            'max_participants' => 12,
            'event_date' => Carbon::now()->addDays(8)->format('Y-m-d'),
            'start_time' => '16:00:00',
            'end_time' => '18:00:00',
            'address' => 'Community Center Basketball Court',
            'voting_expiry_time' => Carbon::now()->addDays(6),
            'status' => 'cancelled',
        ]);

        // Create 15 random events from random users
        $users = User::where('role', 'user')->take(5)->get();

        foreach ($users as $user) {
            Event::factory()
                ->count(3)
                ->state(['user_id' => $user->id])
                ->create();
        }
    }
}
