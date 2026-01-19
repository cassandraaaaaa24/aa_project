<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the tweets for the user.
     */
    public function tweets()
    {
        return $this->hasMany(Tweet::class);
    }

    /**
     * Get the tweets this user has liked.
     */
    public function likedTweets()
    {
        return $this->belongsToMany(Tweet::class, 'tweet_user_likes')
                    ->withTimestamps();
    }

    /**
     * Users that this user is following.
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
                    ->withTimestamps();
    }

    /**
     * Users that are following this user.
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
                    ->withTimestamps();
    }

    /**
     * Check if this user is following another user.
     */
    public function isFollowing($userId)
    {
        return $this->following()->where('following_id', $userId)->exists();
    }

    /**
     * Follow a user.
     */
    public function follow($userId)
    {
        if ($this->id === $userId) {
            return false; // Can't follow yourself
        }

        if (!$this->isFollowing($userId)) {
            $this->following()->attach($userId);
            return true;
        }

        return false;
    }

    /**
     * Unfollow a user.
     */
    public function unfollow($userId)
    {
        if ($this->isFollowing($userId)) {
            $this->following()->detach($userId);
            return true;
        }

        return false;
    }
}