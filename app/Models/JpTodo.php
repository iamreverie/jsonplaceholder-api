<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JpTodo extends Model
{
    protected $table = 'jp_todos';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = ['id', 'user_id', 'title', 'completed'];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(JpUser::class, 'user_id');
    }
}