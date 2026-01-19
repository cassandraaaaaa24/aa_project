<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Tweets authored by this user.
     */
    public function tweets()
    {
        return $this->hasMany(Tweet::class);
    }

    /**
     * Tweets this user has liked.
     */
    public function likedTweets()
    {
        return $this->belongsToMany(Tweet::class, 'tweet_user_likes')
                    ->withTimestamps();
    }
    
}
