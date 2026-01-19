<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'likes_count',
    ];

    protected $casts = [
        'likes_count' => 'integer',
    ];

    /**
     * Get the user that owns the tweet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Users who liked this tweet.
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'tweet_user_likes')
                    ->withTimestamps();
    }
}
