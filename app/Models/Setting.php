<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = ['key', 'value'];

    public const DEFAULTS = [
        'site_name' => 'Call Guru',
        'theme_color' => '#ED701D',
        'site_logo' => null,
        'site_favicon' => null,
    ];

    public static function allSettings(): array
    {
        return Cache::rememberForever('app_settings', function (): array {
            if (! Schema::hasTable('settings')) {
                return self::DEFAULTS;
            }

            return array_replace(
                self::DEFAULTS,
                self::query()->pluck('value', 'key')->all()
            );
        });
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return self::allSettings()[$key] ?? $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }
}
