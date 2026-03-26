<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpPost extends Model
{
    protected $table = 'jp_posts';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'user_id', 'title', 'body'];

    public function user()
    {
        return $this->belongsTo(JpUser::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(JpComment::class, 'post_id');
    }
}