<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $startTime = $this->faker->time();
        $endTime = date('H:i:s', strtotime($startTime) + 7200); // 2 hours after start

        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'image_path' => null,
            'min_participants' => $this->faker->numberBetween(2, 5),
            'max_participants' => $this->faker->numberBetween(10, 30),
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'address' => $this->faker->address(),
            'voting_expiry_time' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
            'status' => 'active',
        ];
    }

    public function sportEvent()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Football Match: ' . $this->faker->words(2, true),
                'min_participants' => 6,
                'max_participants' => 22,
            ];
        });
    }

    public function meetupEvent()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Coffee Meetup: ' . $this->faker->words(2, true),
                'min_participants' => 2,
                'max_participants' => 10,
            ];
        });
    }

    public function travelEvent()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Trip to ' . $this->faker->city(),
                'min_participants' => 3,
                'max_participants' => 15,
                'event_date' => $this->faker->dateTimeBetween('+2 weeks', '+4 months')->format('Y-m-d'),
            ];
        });
    }
}
