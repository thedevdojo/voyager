<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Resizable;
use TCG\Voyager\Traits\Translatable;

class Post extends Model
{
    use Translatable;
    use Resizable;

    protected $translatable = ['title', 'seo_title', 'excerpt', 'body', 'slug', 'meta_description', 'meta_keywords'];

    public const PUBLISHED = 'PUBLISHED';

    protected $guarded = [];

    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->author_id && Auth::user()) {
            $this->author_id = Auth::user()->getKey();
        }

        return parent::save();
    }

    public function authorId()
    {
        return $this->belongsTo(Voyager::modelClass('User'), 'author_id', 'id');
    }

    /**
     * Scope a query to only published scopes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished(Builder $query)
    {
        return $query->where('status', '=', static::PUBLISHED);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo(Voyager::modelClass('Category'));
    }
}
