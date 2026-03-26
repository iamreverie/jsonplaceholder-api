<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpPhoto extends Model
{
    protected $table = 'jp_photos';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'album_id', 'title', 'url', 'thumbnail_url'];

    public function album()
    {
        return $this->belongsTo(JpAlbum::class, 'album_id');
    }
}