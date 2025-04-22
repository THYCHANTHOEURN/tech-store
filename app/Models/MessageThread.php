<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MessageThread extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subject',
        'status',
        'last_message_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            $thread->uuid = (string) Str::uuid();
        });
    }

    /**
     * Get the user who owns the thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages associated with the thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    /**
     * Get the last message in the thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'thread_id')->latest();
    }

    /**
     * Scope a query to only include active threads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope a query to only include closed threads.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
