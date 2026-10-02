<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * One scheduled payment, e.g. "Advance" or "Roofing".
 *
 * Its status is never stored. It is worked out from the most recent receipt,
 * so there is a single source of truth and the two can never disagree.
 */
class PaymentInstalment extends Model
{
    public const STATUS_LABELS = [
        'due'       => 'Payment due',
        'submitted' => 'Receipt under review',
        'paid'      => 'Paid',
        'rejected'  => 'Receipt rejected',
    ];

    protected $fillable = ['client_project_id', 'label', 'amount', 'due_date', 'sort_order'];

    protected $casts = [
        'amount'   => 'decimal:2',
        'due_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class)->latest('id');
    }

    public function latestReceipt(): HasOne
    {
        return $this->hasOne(PaymentReceipt::class)->latestOfMany();
    }

    public function getStatusAttribute(): string
    {
        return match ($this->latestReceipt?->status) {
            'pending'  => 'submitted',
            'verified' => 'paid',
            'rejected' => 'rejected',
            default    => 'due',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status];
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'paid'      => 'bg-moss/15 text-moss',
            'submitted' => 'bg-draft/15 text-draft',
            'rejected'  => 'bg-clay/15 text-clay',
            default     => 'bg-ink/10 text-ink/70',
        };
    }

    /** A receipt can be uploaded until one is accepted or while one awaits review. */
    public function getCanUploadAttribute(): bool
    {
        return in_array($this->status, ['due', 'rejected'], true);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'due' && $this->due_date?->isPast();
    }
}
