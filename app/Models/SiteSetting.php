<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value', 'group'])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    /**
     * In-memory cache for settings during request lifecycle.
     *
     * @var array<string, string|null>|null
     */
    protected static ?array $runtimeCache = null;

    /**
     * Retrieve a setting value by key with optional default fallback.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        if (static::$runtimeCache === null) {
            static::$runtimeCache = static::pluck('value', 'key')->toArray();
        }

        return static::$runtimeCache[$key] ?? $default;
    }

    /**
     * Store or update a setting value.
     */
    public static function set(string $key, ?string $value, string $group = 'general'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        if (static::$runtimeCache !== null) {
            static::$runtimeCache[$key] = $value;
        }

        return $setting;
    }

    /**
     * Return all settings as key => value dictionary.
     *
     * @return array<string, string|null>
     */
    public static function allKeyValues(): array
    {
        if (static::$runtimeCache === null) {
            static::$runtimeCache = static::pluck('value', 'key')->toArray();
        }

        return static::$runtimeCache;
    }

    /**
     * Clear runtime cache (useful after bulk updates or in tests).
     */
    public static function clearRuntimeCache(): void
    {
        static::$runtimeCache = null;
    }
}
