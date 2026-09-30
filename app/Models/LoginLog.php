<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * One security event: a sign-in attempt, a lockout, or blocked page access.
 *
 * The table is still called login_logs because it began as a login audit
 * trail; it was extended rather than replaced so no history was lost.
 */
class LoginLog extends Model
{
    public const LOGIN         = 'login';
    public const LOCKOUT       = 'lockout';
    public const ACCESS_DENIED = 'access_denied';

    protected $fillable = [
        'admin_id', 'email', 'event', 'path', 'successful',
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
     * Records a sign-in attempt, or a lockout. Called from AuthController.
     */
    public static function record(string $email, bool $successful, ?int $adminId, $request, string $event = self::LOGIN): void
    {
        static::create([
            'admin_id'     => $adminId,
            'email'        => $email,
            'event'        => $event,
            'successful'   => $successful,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 512),
            'attempted_at' => now(),
        ]);
    }

    /**
     * Records a logged-in admin being refused a restricted page.
     * Called from the PrimaryAdmin middleware.
     */
    public static function recordAccessDenied(Request $request): void
    {
        $adminId = session('admin_id');

        static::create([
            'admin_id'     => $adminId,
            'email'        => AdminUser::whereKey($adminId)->value('email') ?? 'unknown',
            'event'        => self::ACCESS_DENIED,
            'path'         => '/' . $request->path(),
            'successful'   => false,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 512),
            'attempted_at' => now(),
        ]);
    }

    /** Label shown in the Result column. */
    public function getResultLabelAttribute(): string
    {
        return match (true) {
            $this->event === self::ACCESS_DENIED => 'Access denied',
            $this->event === self::LOCKOUT       => 'Locked out',
            $this->successful                    => 'Success',
            default                              => 'Failed',
        };
    }

    /** Colour of the Result pill, so the event types are distinguishable at a glance. */
    public function getResultClassAttribute(): string
    {
        return match (true) {
            $this->event === self::ACCESS_DENIED => 'bg-draft/15 text-draft',
            $this->event === self::LOCKOUT       => 'bg-clay text-white',
            $this->successful                    => 'bg-moss/15 text-moss',
            default                              => 'bg-clay/15 text-clay',
        };
    }

    /** Short explanation shown in the Details column. */
    public function getDetailAttribute(): string
    {
        return match (true) {
            $this->event === self::ACCESS_DENIED => "Tried to open {$this->path}",
            $this->event === self::LOCKOUT       => 'Refused — account locked',
            $this->successful                    => 'Signed in',
            $this->admin_id === null             => 'No account with this email',
            default                              => 'Wrong password',
        };
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

    // ── Scopes ────────────────────────────────────────────

    /** Failed sign-ins, including attempts refused during a lockout. */
    public function scopeFailed($query)
    {
        return $query->whereIn('event', [self::LOGIN, self::LOCKOUT])
                     ->where('successful', false);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('event', self::LOGIN)->where('successful', true);
    }

    public function scopeAccessDenied($query)
    {
        return $query->where('event', self::ACCESS_DENIED);
    }
}
