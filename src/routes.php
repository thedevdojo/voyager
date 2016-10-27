<?php 

use Illuminate\Http\Request;

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

	Route::get('admin/menus/{id}/builder/', function($id){
		$menu = Menu::find($id);
		return view('voyager::menus.builder', array('menu' => $menu) );
	});

	Route::delete('/admin/menu/delete_menu_item/{id}', function($id){
		$item = TCG\Voyager\Models\MenuItem::find($id);
		$menu_id = $item->menu_id;
		$item->destroy($id);
		return redirect('/admin/menus/' . $menu_id . '/builder')->with(array('message' => 'Successfully Deleted Menu Item.', 'alert-type' => 'success'));
	});

	Route::post('/admin/menu/add_item', function(Request $request){
		$data = $request->all();
		$highest_order_menu_item = TCG\Voyager\Models\MenuItem::where('parent_id', '=', NULL)->orderBy('order', 'DESC')->first();
		if(isset($highest_order_menu_item->id)){
			$data['order'] = intval($highest_order_menu_item->order) + 1;
		} else {
			$data['order'] = 1;
		}
		TCG\Voyager\Models\MenuItem::create($data);
		return redirect('/admin/menus/' . $data['menu_id'] . '/builder')->with(array('message' => 'Successfully Created New Menu Item.', 'alert-type' => 'success'));
	});

	Route::put('/admin/menu/update_menu_item/', function(Request $request){
		$id = $request->input('id');
		$data = $request->all();
		unset($data['id']);
		$menu_item = TCG\Voyager\Models\MenuItem::find($id);
		$menu_item->update($data);
		return redirect('/admin/menus/' . $menu_item->menu_id . '/builder')->with(array('message' => 'Successfully Updated Menu Item.', 'alert-type' => 'success'));
	});

	Route::post('/admin/menu/order', function(Request $request){
		$menu_item_order = json_decode($request->input('order'));
		order_menu($menu_item_order, NULL);
	});

	function order_menu($menu_items, $parent_id){
		foreach($menu_items as $index => $menu_item):
			$item = TCG\Voyager\Models\MenuItem::find($menu_item->id);
			$item->order = $index + 1;
			$item->parent_id = $parent_id;
			$item->save();
			if(isset($menu_item->children)){
				order_menu($menu_item->children, $item->id);
			}
		endforeach;
	}

	//Route::resource('admin/roles', 'TCG\Voyager\Controllers\VoyagerRoleController');

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