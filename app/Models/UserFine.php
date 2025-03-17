<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'reason',
        'duration_days',
        'fine_until',
    ];

    protected $casts = [
        'fine_until' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function isActive(): bool
    {
        return $this->fine_until > now();
    }
}
