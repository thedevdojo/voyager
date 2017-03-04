<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Page extends Model
{
    /**
     * Statuses.
     */
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    /**
     * List of statuses.
     *
     * @var array
     */
    public static $statuses = [self::STATUS_ACTIVE, self::STATUS_INACTIVE];

    protected $guarded = [];

    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->author_id && Auth::user()) {
            $this->author_id = Auth::user()->id;
        }

        parent::save();
    }

    /**
     * Scope a query to only include active pages.
     *
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    /**
     * @param null $slug
     * @param null $id
     * @param bool $author
     *
     * @return \Illuminate\Support\HtmlString
     *
     * @author Dusan Perisic
     */
    public static function display(string $slug = null, string $id = null, array $page_author = null)
    {
        $data = static::where($slug ? 'slug' : 'id', $slug ? $slug : $id)->get()->first();
        $data->author = $page_author;
        if ($data->author) {
            $data->author = User::find($data->author_id, $page_author)->toArray();
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('voyager::posts.show', $data)->render()
        );
    }
}
