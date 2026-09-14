<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    protected $fillable = [
        'admin_id', 'email', 'successful',
        'ip_address', 'user_agent', 'attempted_at',
    ];

    protected $casts = [
        'successful'   => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_id');
    }

    /**
     * Records one attempt. Called from AuthController on both paths.
     */
    public static function record(string $email, bool $successful, ?int $adminId, $request): void
    {
        static::create([
            'admin_id'     => $adminId,
            'email'        => $email,
            'successful'   => $successful,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 512),
            'attempted_at' => now(),
        ]);
    }

    /** Shortens the user agent to something readable in a table. */
    public function getBrowserAttribute(): string
    {
        $ua = (string) $this->user_agent;

        foreach (['Edg' => 'Edge', 'OPR' => 'Opera', 'Chrome' => 'Chrome',
                  'Firefox' => 'Firefox', 'Safari' => 'Safari'] as $needle => $name) {
            if (str_contains($ua, $needle)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    public function scopeFailed($query)
    {
        return $query->where('successful', false);
    }
}
