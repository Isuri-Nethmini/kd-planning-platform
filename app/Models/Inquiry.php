<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'house_plan_id',
        'message',
        'status',
    ];

    public function housePlan(): BelongsTo
    {
        return $this->belongsTo(HousePlan::class);
    }

    // ── Scopes ────────────────────────────────────────────
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
