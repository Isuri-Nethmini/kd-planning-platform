<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\ResolvesImageUrl;

class ProjectImage extends Model
{
    use HasFactory;
    use ResolvesImageUrl;

    protected $fillable = [
        'completed_project_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function completedProject(): BelongsTo
    {
        return $this->belongsTo(CompletedProject::class);
    }

    /**
     * Convenience accessor: $image->url
     */
    public function getUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->image_path);
    }
}
