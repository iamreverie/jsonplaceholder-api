<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpUser extends Model
{
    protected $table = 'jp_users';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id', 'name', 'username', 'email',
        'phone', 'website', 'address', 'company',
    ];

    protected $casts = [
        'address' => 'array',
        'company' => 'array',
    ];

    public function posts()
    {
        return $this->hasMany(JpPost::class, 'user_id');
    }

    public function albums()
    {
        return $this->hasMany(JpAlbum::class, 'user_id');
    }

    public function todos()
    {
        return $this->hasMany(JpTodo::class, 'user_id');
    }
}