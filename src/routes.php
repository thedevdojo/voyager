<?php 

Route::get('admin/login', 'TCG\Voyager\Controllers\VoyagerAuthController@login')->middleware('web');
Route::post('admin/login', 'TCG\Voyager\Controllers\VoyagerAuthController@postLogin')->middleware('web');

Route::group(['middleware' => ['web', 'admin.user']], function () {

	// Main Admin and Logout Route
	Route::get('admin', 'TCG\Voyager\Controllers\VoyagerController@index');
	Route::get('admin/logout', 'TCG\Voyager\Controllers\VoyagerController@logout');
	Route::post('admin/upload', 'TCG\Voyager\Controllers\VoyagerController@upload');

	Route::get('/admin/profile', function(){
		return view('voyager::profile');
	});

	if(Schema::hasTable('data_types')):
		foreach(TCG\Voyager\Models\DataType::all() as $dataTypes):
			Route::resource('admin/' . $dataTypes->slug, 'TCG\Voyager\Controllers\VoyagerBreadController');
		endforeach;
	endif;

	// Menu Routes
	Route::get('admin/menus/{id}/builder/', 'TCG\Voyager\Controllers\VoyagerMenuController@builder');
	Route::delete('/admin/menu/delete_menu_item/{id}', 'TCG\Voyager\Controllers\VoyagerMenuController@delete_menu');
	Route::post('/admin/menu/add_item', 'TCG\Voyager\Controllers\VoyagerMenuController@add_item');
	Route::put('/admin/menu/update_menu_item/', 'TCG\Voyager\Controllers\VoyagerMenuController@update_item');
	Route::post('/admin/menu/order', 'TCG\Voyager\Controllers\VoyagerMenuController@order_item');

	// Settings
	Route::get('admin/settings', 'TCG\Voyager\Controllers\VoyagerSettingsController@index');
	Route::post('admin/settings', 'TCG\Voyager\Controllers\VoyagerSettingsController@save');
	Route::post('admin/settings/create', 'TCG\Voyager\Controllers\VoyagerSettingsController@create');
	Route::delete('admin/settings/{id}', 'TCG\Voyager\Controllers\VoyagerSettingsController@delete');
	Route::get('admin/settings/move_up/{id}', 'TCG\Voyager\Controllers\VoyagerSettingsController@move_up');
	Route::get('admin/settings/move_down/{id}', 'TCG\Voyager\Controllers\VoyagerSettingsController@move_down');
	Route::get('admin/settings/delete_value/{id}', 'TCG\Voyager\Controllers\VoyagerSettingsController@delete_value');

	// Admin Media
	Route::get('admin/media', 'TCG\Voyager\Controllers\VoyagerMediaController@index');
	Route::post('admin/media/files', 'TCG\Voyager\Controllers\VoyagerMediaController@files');
    Route::post('admin/media/new_folder', 'TCG\Voyager\Controllers\VoyagerMediaController@new_folder');
    Route::post('admin/media/delete_file_folder', 'TCG\Voyager\Controllers\VoyagerMediaController@delete_file_folder');
    Route::post('admin/media/directories', 'TCG\Voyager\Controllers\VoyagerMediaController@get_all_dirs');
    Route::post('admin/media/move_file', 'TCG\Voyager\Controllers\VoyagerMediaController@move_file');
    Route::post('admin/media/rename_file', 'TCG\Voyager\Controllers\VoyagerMediaController@rename_file');
    Route::post('admin/media/upload', 'TCG\Voyager\Controllers\VoyagerMediaController@upload');

    // Database Routes
    Route::get('/admin/database', 'TCG\Voyager\Controllers\VoyagerDatabaseController@index');
	Route::get('/admin/database/create-table', ['as' => 'voyager_database_create_table', 'uses' => 'TCG\Voyager\Controllers\VoyagerDatabaseController@create']);
	Route::post('/admin/database/create-table', 'TCG\Voyager\Controllers\VoyagerDatabaseController@store');
	Route::get('/admin/database/table/{table}', 'TCG\Voyager\Controllers\VoyagerDatabaseController@table');
	Route::delete('/admin/database/table/delete/{table}', 'TCG\Voyager\Controllers\VoyagerDatabaseController@delete');
	Route::get('/admin/database/edit-{table}-table', 'TCG\Voyager\Controllers\VoyagerDatabaseController@edit');
	Route::post('/admin/database/edit-table', 'TCG\Voyager\Controllers\VoyagerDatabaseController@update');
	Route::get('test_order', function(){
		Schema::table('latestbbc', function($table){
			$table->string('slug')->after('id')->change();
		});
	});

	Route::post('/admin/database/create_bread', 'TCG\Voyager\Controllers\VoyagerDatabaseController@addBread');
	Route::post('/admin/database/store_bread', 'TCG\Voyager\Controllers\VoyagerDatabaseController@storeBread');
	Route::get('/admin/database/{id}/edit-bread', 'TCG\Voyager\Controllers\VoyagerDatabaseController@addEditBread');
	Route::put('/admin/database/{id}/edit-bread', 'TCG\Voyager\Controllers\VoyagerDatabaseController@updateBread');
	Route::delete('/admin/database/delete_bread/{id}', 'TCG\Voyager\Controllers\VoyagerDatabaseController@deleteBread');

});