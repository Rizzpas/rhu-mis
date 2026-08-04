<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    /**
     * Get a single setting value by key.
     */
    public static function get(string $key, $default = null): ?string
    {
        $settings = Cache::remember('site_settings_all', 300, function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Get a JSON setting decoded as array.
     */
    public static function getJson(string $key, array $default = []): array
    {
        $value = static::get($key);
        if ($value) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : $default;
        }

        return $default;
    }

    /**
     * Get all settings for a given group as key => value pairs.
     */
    public static function getGroup(string $group): array
    {
        $settings = Cache::remember('site_settings_all', 300, function () {
            return static::all();
        });

        // If cached as array of key=>value (from get()), re-fetch with group info
        if (! ($settings instanceof \Illuminate\Database\Eloquent\Collection)) {
            $settings = static::all();
        }

        return $settings
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Set a setting value and clear cache.
     */
    public static function set(string $key, ?string $value, string $group = 'general', string $type = 'string'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group, 'type' => $type]);
        Cache::forget('site_settings_all');
    }

    /**
     * Clear the settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings_all');
    }
}
