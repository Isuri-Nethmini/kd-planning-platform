<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectStage extends Model
{
    public const STATUSES = [
        'pending'     => 'Not started',
        'in_progress' => 'In progress',
        'completed'   => 'Completed',
    ];

    protected $fillable = ['client_project_id', 'name', 'sort_order', 'status', 'started_on', 'completed_on', 'note'];

    protected $casts = [
        'started_on'   => 'date',
        'completed_on' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(StagePhoto::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'completed'   => 'bg-moss/15 text-moss',
            'in_progress' => 'bg-draft/15 text-draft',
            default       => 'bg-ink/10 text-ink/70',
        };
    }
}
