<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key, with optional default.
     * Usage: SystemSetting::get('whatsapp_number')
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Set (or update) a setting value by key.
     * Usage: SystemSetting::set('whatsapp_number', '+94717261930')
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }
}
