<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectImage extends Model
{
    use HasFactory;

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
     *
     * Resolves against the "public" disk explicitly. Using the default disk
     * here was a bug: the default is "local", whose root is storage/app/private,
     * so uploaded images produced URLs that pointed at a path where the file
     * did not exist. Swapping the disk name here (or the disk's driver in
     * config/filesystems.php) is the single change needed to move to S3 later.
     *
     * Absolute URLs are passed through untouched so a CDN can be mixed in.
     */
    public function getUrlAttribute(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        return str_starts_with($this->image_path, 'http')
            ? $this->image_path
            : Storage::disk('public')->url($this->image_path);
    }
}
