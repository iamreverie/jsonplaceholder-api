<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpAlbum extends Model
{
    protected $table = 'jp_albums';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'user_id', 'title'];

    public function user()
    {
        return $this->belongsTo(JpUser::class, 'user_id');
    }

    public function photos()
    {
        return $this->hasMany(JpPhoto::class, 'album_id');
    }
}