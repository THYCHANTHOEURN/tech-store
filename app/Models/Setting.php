<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use \Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'options',  // Add this field to support select options for theme settings
        'label',
        'description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever('setting.' . $key, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('setting.' . $key);

        // No return value as the method should return null
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByGroup(string $group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * Get image URL for image type settings
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if ($this->type === 'image' && $this->value) {
            return Storage::url($this->value);
        }

        return null;
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function getOptionsArrayAttribute()
    {
        if ($this->options) {
            return json_decode($this->options, true);
        }

        return [];
    }

    /**
     * Get inventory settings
     *
     * @return array
     */
    public static function getInventorySettings()
    {
        return [
            'low_stock_threshold'       => (int) self::get('low_stock_threshold', 10),
            'critical_stock_threshold'  => (int) self::get('critical_stock_threshold', 5),
            'overstock_threshold'       => (int) self::get('overstock_threshold', 1000),
            'enable_stock_alerts'       => (bool) self::get('enable_stock_alerts', true),
        ];
    }

    protected static $logAttributes = ['key', 'value', 'group', 'type', 'options', 'label', 'description'];
    protected static $logName = 'setting';
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(self::$logAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
