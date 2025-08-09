<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use \Spatie\Activitylog\Traits\LogsActivity;

class Banner extends Model
{
    /** @use HasFactory<\Database\Factories\BannerFactory> */
    use HasFactory;
    use HasUuidTrait;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banners';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'title',
        'image',
        'link',
        'status',
        'position',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * The attributes that should be appended.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Banner position constants
     */
    const POSITION_SLIDER   = 'slider';
    const POSITION_SIDE     = 'side';
    const POSITION_PROMO    = 'promo';

    /**
     * Scope a query to only include active banners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to get banners by position.
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get the image URL attribute.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        return $this->image ? Storage::url($this->image) : '';
    }

    protected static $logAttributes = ['title', 'image', 'link', 'status', 'position'];
    protected static $logName = 'banner';
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
