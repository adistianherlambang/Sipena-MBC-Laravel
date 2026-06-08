<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value'];

    public static function getValue(string $key, string $default = ''): string
    {
        $setting = self::where('setting_key', $key)->first();

        return $setting ? (string) $setting->setting_value : $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        self::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }
}
