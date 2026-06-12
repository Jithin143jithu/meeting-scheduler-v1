<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'meeting_type_id', 'guest_name', 'guest_email', 'guest_phone',
        'guest_notes', 'start_time', 'end_time', 'location_url', 'status',
        'payment_status', 'amount', 'timezone', 'meeting_link', 'meeting_notes',
        'confirmed_at', 'cancelled_at'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meetingType(): BelongsTo
    {
        return $this->belongsTo(MeetingType::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(BookingNote::class);
    }
}
