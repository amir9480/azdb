<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buisness extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'approved',
        'name',
        'description',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'approved' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Staff users that can access panel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Buisness URL
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        return route('tickets.index', ['buisness' => $this->id]);
    }

    /**
     * Buisness Short URL
     *
     * @return string
     */
    public function getShortLinkAttribute()
    {
        return route('buisness', ['buisness' => $this->id]);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('approved', function ($query) {
            $query->where('approved', 1);
        });
    }
}
