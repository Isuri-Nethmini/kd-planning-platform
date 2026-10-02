<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * A confirmed client with access to the client portal.
 *
 * Authenticates through Laravel's "customer" guard, which is entirely separate
 * from the admin session, so a customer can never reach the admin panel.
 */
class Customer extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'must_change_password',
        'password_changed_at', 'last_login_at', 'inquiry_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'             => 'hashed',
        'must_change_password' => 'boolean',
        'password_changed_at'  => 'datetime',
        'last_login_at'        => 'datetime',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(ClientProject::class)->latest();
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }
}
