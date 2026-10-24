<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;

    /**
     * The sales pipeline, in order.
     *
     * Kept here rather than in a database ENUM so a new stage is a one-line
     * change. Validation rules and the admin UI both read from this list.
     */
    public const STATUSES = [
        'new'       => 'New',
        'read'      => 'Read',
        'quoted'    => 'Quoted',
        'converted' => 'Converted',
        'closed'    => 'Closed',
    ];

    /** Stages that count as a completed sale. */
    public const WON = ['converted'];

    /** Choices for the general inquiry form. Edit the labels here to change them everywhere. */
    public const BUDGETS = [
        'under_5m' => 'Under Rs. 5 million',
        '5m_10m'   => 'Rs. 5 – 10 million',
        '10m_20m'  => 'Rs. 10 – 20 million',
        'over_20m' => 'Over Rs. 20 million',
        'not_sure' => 'Not sure yet',
    ];

    public const LAND_SIZES = [
        'under_10' => 'Under 10 perches',
        '10_20'    => '10 – 20 perches',
        '20_40'    => '20 – 40 perches',
        'over_40'  => 'Over 40 perches',
        'not_sure' => 'Not sure yet',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'house_plan_id',
        'completed_project_id',
        'message',
        'budget',
        'bedrooms',
        'bathrooms',
        'floors',
        'land_size',
        'status',
        'admin_notes',
        'quoted_amount',
        'responded_at',
    ];

    protected $casts = [
        'quoted_amount' => 'decimal:2',
        'responded_at'  => 'datetime',
    ];

    public function housePlan(): BelongsTo
    {
        return $this->belongsTo(HousePlan::class);
    }

    /**
     * Set when the buyer asked for a design based on a house KD already built,
     * rather than picking something from the catalogue.
     */
    public function completedProject(): BelongsTo
    {
        return $this->belongsTo(CompletedProject::class);
    }

    /** Set once this inquiry has been turned into a client account. */
    public function customer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /** Short human label for whatever this inquiry is about. */
    public function getSubjectLabelAttribute(): string
    {
        if ($this->housePlan) {
            return $this->housePlan->name;
        }

        if ($this->completedProject) {
            return 'Similar to: ' . $this->completedProject->title;
        }

        return 'General inquiry';
    }

    /**
     * The requirements the buyer filled in, as label => value, skipping blanks.
     * Used by the admin page, the notification email and the CSV report.
     */
    public function getRequirementsAttribute(): array
    {
        return array_filter([
            'Budget'    => self::BUDGETS[$this->budget] ?? null,
            'Bedrooms'  => $this->bedrooms ? ($this->bedrooms >= 6 ? '6 or more' : (string) $this->bedrooms) : null,
            'Bathrooms' => $this->bathrooms ? ($this->bathrooms >= 5 ? '5 or more' : (string) $this->bathrooms) : null,
            'Floors'    => $this->floors ? ($this->floors >= 3 ? '3 or more' : (string) $this->floors) : null,
            'Land size' => self::LAND_SIZES[$this->land_size] ?? null,
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Tailwind classes for the status pill, so the colour coding stays
     * consistent everywhere the status is displayed.
     */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'new'       => 'bg-clay/10 text-clay',
            'quoted'    => 'bg-draft/10 text-draft',
            'converted' => 'bg-moss/10 text-moss',
            'closed'    => 'bg-ink/10 text-ink/40',
            default     => 'bg-ink/5 text-ink/50',
        };
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeWon($query)
    {
        return $query->whereIn('status', self::WON);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
