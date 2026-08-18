<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\ResolvesImageUrl;

class PlanImage extends Model
{
    use HasFactory;
    use ResolvesImageUrl;

    protected $fillable = [
        'house_plan_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function housePlan(): BelongsTo
    {
        return $this->belongsTo(HousePlan::class);
    }

    // Convenience accessor: $image->url
    /**
     * Convenience accessor: $image->url
     */
    public function getUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->image_path);
    }
}
