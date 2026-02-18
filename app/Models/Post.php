<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = ['user_id', 'category_id', 'title', 'slug', 'content'];

    // Post należy do użytkownika (autora)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Post należy do kategorii
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Post może mieć wiele komentarzy
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
