<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Turns a stored image path into a usable URL.
 *
 * Three kinds of path can appear in the database, and they resolve differently:
 *
 *   1. "https://cdn.example.com/x.jpg"  — absolute, returned untouched so a CDN
 *      can be introduced later without a migration.
 *   2. "media/projects/malik.jpg"       — an asset that ships with the
 *      repository under public/. Used for seeded client content that is part of
 *      the codebase rather than something an admin uploaded.
 *   3. "plans/13/abc.jpg"               — a genuine upload on the "public" disk,
 *      served through the public/storage symlink.
 *
 * Case 3 is deliberately resolved against the "public" disk by name rather than
 * the default disk: the default is "local", whose root is storage/app/private,
 * which produced URLs pointing at a path where the file did not exist.
 */
trait ResolvesImageUrl
{
    protected function resolveImageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'media/')) {
            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}
