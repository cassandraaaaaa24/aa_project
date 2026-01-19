<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    /**
     * Tweets tagged with this tag.
     */
    public function tweets()
    {
        return $this->belongsToMany(Tweet::class, 'tweet_tags')
                    ->withTimestamps();
    }
}
