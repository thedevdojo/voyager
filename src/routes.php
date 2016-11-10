<?php 

Route::get('login', ['uses'=>'VoyagerAuthController@login', 'as'=>'voyager.login'])->middleware('web');
Route::post('login', 'VoyagerAuthController@postLogin')->middleware('web');

Route::group(['middleware' => ['web', 'admin.user']], function () {

	// Main Admin and Logout Route
	Route::get('/', ['uses'=>'VoyagerController@index', 'as'=>'voyager.dashboard']);
	Route::get('logout', ['uses'=>'VoyagerController@logout', 'as'=>'voyager.logout']);
	Route::post('upload', ['uses'=>'VoyagerController@upload', 'as'=>'voyager.upload']);

	Route::get('profile', ['as'=>'voyager.profile', function(){
		return view('voyager::profile');
	}]);

	if(env('DB_CONNECTION') !== null && Schema::hasTable('data_types')):
		foreach(TCG\Voyager\Models\DataType::all() as $dataTypes):
			Route::resource($dataTypes->slug, 'VoyagerBreadController');
		endforeach;
	endif;

	// Menu Routes
	Route::get('menus/{id}/builder/', ['uses'=>'VoyagerMenuController@builder', 'as'=>'voyager.menu.builder']);
	Route::delete('menu/delete_menu_item/{id}', ['uses'=>'VoyagerMenuController@delete_menu', 'as'=>'voyager.menu.delete_menu_item']);
	Route::post('menu/add_item', ['uses'=>'VoyagerMenuController@add_item', 'as'=>'voyager.menu.add_item']);
	Route::put('menu/update_menu_item', ['uses'=>'VoyagerMenuController@update_item', 'as'=>'voyager.menu.update_menu_item']);
	Route::post('menu/order', ['uses'=>'VoyagerMenuController@order_item', 'as'=>'voyager.menu.order_item']);

	// Settings
	Route::get('settings', ['uses'=>'VoyagerSettingsController@index', 'as'=>'voyager.settings']);
	Route::post('settings', 'VoyagerSettingsController@save');
	Route::post('settings/create', ['uses'=>'VoyagerSettingsController@create', 'as'=>'voyager.settings.create']);
	Route::delete('settings/{id}', ['uses'=>'VoyagerSettingsController@delete', 'as'=>'voyager.settings.delete']);
	Route::get('settings/move_up/{id}', ['uses'=>'VoyagerSettingsController@move_up', 'as'=>'voyager.settings.move_up']);
	Route::get('settings/move_down/{id}', ['uses'=>'VoyagerSettingsController@move_down', 'as'=>'voyager.settings.move_down']);
	Route::get('settings/delete_value/{id}', ['uses'=>'VoyagerSettingsController@delete_value', 'as'=>'voyager.settings.delete_value']);

	// Admin Media
	Route::get('media', ['uses'=>'VoyagerMediaController@index', 'as'=>'voyager.media']);
	Route::post('media/files', 'VoyagerMediaController@files');
    Route::post('media/new_folder', 'VoyagerMediaController@new_folder');
    Route::post('media/delete_file_folder', 'VoyagerMediaController@delete_file_folder');
    Route::post('media/directories', 'VoyagerMediaController@get_all_dirs');
    Route::post('media/move_file', 'VoyagerMediaController@move_file');
    Route::post('media/rename_file', 'VoyagerMediaController@rename_file');
    Route::post('media/upload', ['uses'=>'VoyagerMediaController@upload', 'as'=>'voyager.media.upload']);

    // Database Routes
    Route::get('database', ['uses'=>'VoyagerDatabaseController@index', 'as'=>'voyager.database']);
	Route::get('database/create-table', ['uses' => 'VoyagerDatabaseController@create', 'as' => 'voyager.database.create_table']);
	Route::post('database/create-table', 'VoyagerDatabaseController@store');
	Route::get('database/table/{table}', ['uses'=>'VoyagerDatabaseController@table', 'as' => 'voyager.database.browse_table']);
	Route::delete('database/table/delete/{table}', 'VoyagerDatabaseController@delete');
	Route::get('database/edit-{table}-table', ['uses'=>'VoyagerDatabaseController@edit', 'as' => 'voyager.database.edit_table']);
	Route::post('database/edit-table', 'VoyagerDatabaseController@update');

    // maiorano84 note: Do not expose test functionality publicly like this
    // TODO: Move this to a separate command (ie: voyager:test_order)
	Route::get('test_order', function(){
		Schema::table('latestbbc', function($table){
			$table->string('slug')->after('id')->change();
		});
	});

	Route::post('database/create_bread', ['uses'=>'VoyagerDatabaseController@addBread', 'as' => 'voyager.database.create_bread']);
	Route::post('database/store_bread', ['uses'=>'VoyagerDatabaseController@storeBread', 'as' => 'voyager.database.store_bread']);
	Route::get('database/{id}/edit-bread', ['uses'=>'VoyagerDatabaseController@addEditBread', 'as' => 'voyager.database.edit_bread']);
	Route::put('database/{id}/edit-bread', 'VoyagerDatabaseController@updateBread');
	Route::delete('database/delete_bread/{id}', ['uses'=>'VoyagerDatabaseController@deleteBread', 'as' => 'voyager.database.delete_bread']);

});