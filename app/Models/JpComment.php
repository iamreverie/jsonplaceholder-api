<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpComment extends Model
{
    protected $table = 'jp_comments';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'post_id', 'name', 'email', 'body'];

    public function post()
    {
        return $this->belongsTo(JpPost::class, 'post_id');
    }
}