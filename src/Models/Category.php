<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    public function posts()
    {
        return $this->hasMany(Post::class)
            ->where('status', '=', 'PUBLISHED')
            ->orderBy('created_at', 'DESC');
    }
}
