<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * A client's build: its construction stages and its payment schedule.
 */
class ClientProject extends Model
{
    public const STATUSES = [
        'active'    => 'In progress',
        'on_hold'   => 'On hold',
        'completed' => 'Completed',
    ];

    /** Every new project starts from these stages; admins can rename or add more. */
    public const DEFAULT_STAGES = [
        'Site preparation',
        'Foundation',
        'Structure and walls',
        'Roofing',
        'Finishing',
        'Handover',
    ];

    protected $fillable = [
        'customer_id', 'house_plan_id', 'title', 'site_location', 'status',
        'start_date', 'estimated_completion', 'contract_value',
    ];

    protected $casts = [
        'start_date'           => 'date',
        'estimated_completion' => 'date',
        'contract_value'       => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function housePlan(): BelongsTo
    {
        return $this->belongsTo(HousePlan::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(ProjectStage::class)->orderBy('sort_order');
    }

    public function instalments(): HasMany
    {
        return $this->hasMany(PaymentInstalment::class)->orderBy('sort_order');
    }

    public function receipts(): HasManyThrough
    {
        return $this->hasManyThrough(PaymentReceipt::class, PaymentInstalment::class);
    }

    public function createDefaultStages(): void
    {
        foreach (self::DEFAULT_STAGES as $i => $name) {
            $this->stages()->create(['name' => $name, 'sort_order' => $i + 1]);
        }
    }

    /** Share of stages completed, as a whole-number percentage. */
    public function getProgressAttribute(): int
    {
        $total = $this->stages->count();

        return $total === 0 ? 0
            : (int) round($this->stages->where('status', 'completed')->count() / $total * 100);
    }

    /** The stage currently under way, or the next one due to start. */
    public function getCurrentStageAttribute(): ?ProjectStage
    {
        return $this->stages->firstWhere('status', 'in_progress')
            ?? $this->stages->firstWhere('status', 'pending');
    }

    public function getScheduledTotalAttribute(): float
    {
        return (float) $this->instalments->sum('amount');
    }

    /** Only verified receipts count as paid — an uploaded receipt is a claim, not a payment. */
    public function getPaidTotalAttribute(): float
    {
        return (float) $this->instalments
            ->filter(fn ($i) => $i->status === 'paid')
            ->sum('amount');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
