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

    // Account changes
    public const ACCOUNT_CREATED  = 'account_created';
    public const PASSWORD_RESET   = 'password_reset';
    public const PASSWORD_CHANGED = 'password_changed';
    public const ACCOUNT_DELETED  = 'account_deleted';

    /** Events that describe a change to an account rather than a sign-in. */
    public const ACCOUNT_EVENTS = [self::ACCOUNT_CREATED, self::PASSWORD_RESET, self::PASSWORD_CHANGED, self::ACCOUNT_DELETED];

    protected $fillable = [
        'admin_id', 'admin_name', 'email', 'event', 'path', 'note', 'successful',
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
            'admin_name'   => $adminId ? AdminUser::whereKey($adminId)->value('name') : null,
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
            'admin_name'   => session('admin_name'),
            'email'        => AdminUser::whereKey($adminId)->value('email') ?? 'unknown',
            'event'        => self::ACCESS_DENIED,
            'path'         => '/' . $request->path(),
            'successful'   => false,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 512),
            'attempted_at' => now(),
        ]);
    }

    /**
     * Records a change to an account: creation, a reset, or a password change.
     *
     * $note explains who did it, e.g. "Created by Dhanushka" — the row itself
     * belongs to the account that was changed.
     */
    public static function recordAccountEvent(AdminUser $admin, string $event, Request $request, ?string $note = null): void
    {
        static::create([
            'admin_id'     => $admin->id,
            'admin_name'   => $admin->name,
            'email'        => $admin->email,
            'event'        => $event,
            'note'         => $note,
            'successful'   => true,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 512),
            'attempted_at' => now(),
        ]);
    }

    /** Label shown in the Result column. */
    public function getResultLabelAttribute(): string
    {
        return match (true) {
            $this->event === self::ACCESS_DENIED    => 'Access denied',
            $this->event === self::LOCKOUT          => 'Locked out',
            $this->event === self::ACCOUNT_CREATED  => 'Account created',
            $this->event === self::PASSWORD_RESET   => 'Password reset',
            $this->event === self::PASSWORD_CHANGED => 'Password changed',
            $this->event === self::ACCOUNT_DELETED  => 'Account deleted',
            $this->successful                    => 'Success',
            default                              => 'Failed',
        };
    }

    /** Colour of the Result pill, so the event types are distinguishable at a glance. */
    public function getResultClassAttribute(): string
    {
        return match (true) {
            $this->event === self::ACCESS_DENIED          => 'bg-draft/15 text-draft',
            $this->event === self::LOCKOUT                => 'bg-clay text-white',
            in_array($this->event, self::ACCOUNT_EVENTS) => 'bg-ink/10 text-ink',
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
            in_array($this->event, self::ACCOUNT_EVENTS) => $this->note ?? 'Account updated',
            $this->successful                    => 'Signed in',
            $this->admin_name === null           => 'No account with this email',
            default                              => 'Wrong password',
        };
    }

    /**
     * Name shown in the Account column.
     *
     * Uses the live account where it still exists, falls back to the name
     * captured at the time for deleted accounts, and only says "Unknown" when
     * no account ever matched — e.g. someone guessing email addresses.
     */
    public function getAccountLabelAttribute(): string
    {
        return match (true) {
            $this->admin !== null      => $this->admin->name,
            $this->admin_name !== null => $this->admin_name . ' (deleted)',
            default                    => 'Unknown account',
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

    public function scopeAccountChanges($query)
    {
        return $query->whereIn('event', self::ACCOUNT_EVENTS);
    }
}
