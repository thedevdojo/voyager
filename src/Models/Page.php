<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public function save(array $options = [])
	{
		// Add the user id as the Author of the post
		$this->author_id = Auth::user()->id;
		parent::save();
		// after save code
	}
}
