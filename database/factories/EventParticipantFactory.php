<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventParticipantFactory extends Factory
{
    protected $model = EventParticipant::class;

    public function definition()
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'status' => 'voted',
        ];
    }

    public function attended()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'attended',
            ];
        });
    }

    public function noShow()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'no_show',
            ];
        });
    }
}
