<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = ['slug', 'name'];

    public function posts()
    {
        return $this->hasMany(Post::class)
            ->published()
            ->orderBy('created_at', 'DESC');
    }

    public function parent_id() {
        return $this->belongsTo(Category::class);
    }    
}
