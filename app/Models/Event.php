<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image_path',
        'min_participants',
        'max_participants',
        'event_date',
        'start_time',
        'end_time',
        'address',
        'voting_expiry_time',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'voting_expiry_time' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(UserFine::class);
    }

    public function isVotingOpen(): bool
    {
        return $this->voting_expiry_time > now();
    }

    public function participantsCount(): int
    {
        return $this->participants()->count();
    }

    public function confirmedParticipantsCount(): int
    {
        return $this->participants()->where('status', 'confirmed')->orWhere('status', 'attended')->count();
    }
}
