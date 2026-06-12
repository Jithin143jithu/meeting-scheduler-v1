<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleAccount extends Model
{
    protected $fillable = [
        'user_id', 'google_id', 'email', 'access_token', 'refresh_token',
        'token_expires_at', 'calendar_id', 'is_synced', 'last_synced_at', 'sync_enabled'
    ];

    protected $casts = [
        'is_synced' => 'boolean',
        'sync_enabled' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
