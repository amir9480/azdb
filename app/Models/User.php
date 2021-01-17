<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Sanjab\Models\SanjabUser;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SanjabUser;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function buisness()
    {
        return $this->hasOne(Buisness::class)->withoutGlobalScope('approved');
    }

    /**
     * Buisnesses that user assigned as support staff.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function buisnesses()
    {
        return $this->belongsToMany(Buisness::class);
    }

    public function getNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
