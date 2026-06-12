<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'description', 'duration', 'location_type',
        'location_url', 'buffer_before', 'buffer_after', 'daily_limit',
        'advance_booking_days', 'min_booking_notice', 'is_active', 'color', 'slug'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
