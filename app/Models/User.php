<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
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
     * Get user's packets.
     *
     * @return  HasMany
     */
    public function packets()
    {
        return $this->hasMany(Packet::class);
    }

    /**
     * Get the User's sessions collection.
     *
     * @return  BelongsToMany
     */
    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'participants', 'user_id');
    }

    /**
     * Get User's Activities.
     *
     * @return  HasMany
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
