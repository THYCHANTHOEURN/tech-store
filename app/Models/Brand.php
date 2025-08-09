<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use \Spatie\Activitylog\Traits\LogsActivity;

class Brand extends Model
{
    /** @use HasFactory<\Database\Factories\BrandFactory> */
    use HasFactory;
    use HasUuidTrait;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'logo',
        'status',
        'description',
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
        'logo_url',
    ];

    /**
     * The attributes that should be logged.
     *
     * @var array
     */
    protected static $logAttributes = ['name', 'slug', 'logo', 'status', 'description'];
    protected static $logName = 'brand';
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    /**
     * Get the products for the brand.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get the logo URL attribute.
     *
     * @return string
     */
    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? Storage::url($this->logo) : '';
    }

    /**
     * Get the activity log options for the model.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly(self::$logAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
