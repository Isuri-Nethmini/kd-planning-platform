<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;
    use ResolvesImageUrl;

    protected $fillable = [
        'title',
        'slug',
        'cover_image',
        'content',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Resolve the stored cover image path to a usable URL.
     * Returns null when no cover has been uploaded yet, which lets
     * <x-image-frame> fall back to the blueprint placeholder.
     */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->cover_image);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->content), 160);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
