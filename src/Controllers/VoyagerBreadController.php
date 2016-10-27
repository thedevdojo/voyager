<?php

namespace TCG\Voyager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use TCG\Voyager\Models\User as User;
use TCG\Voyager\Models\DataType as DataType;
use Intervention\Image\Facades\Image as Image;
use \Storage;

class VoyagerBreadController extends Controller
{
    //***************************************
    //               ____
    //              |  _ \
    //              | |_) |
    //              |  _ <
    //              | |_) |
    //              |____/
    //
    //      Browse our Data Type (B)READ
    //
    //****************************************

  public function index(Request $request)
  {
    // GET THE SLUG, ex. 'posts', 'pages', etc.
    $slug = $request->segment(2);

    // GET THE DataType based on the slug
    $dataType = DataType::where('slug', '=', $slug)->first();

    // Next Get the actual content from the MODEL that corresponds to the slug DataType
    eval('$dataTypeContent = ' . $dataType->model_name . '::all();');


    if(view()->exists('admin.' . $slug . '.browse')){
      return view('admin.' . $slug . '.browse', array('dataType' => $dataType, 'dataTypeContent' => $dataTypeContent)); 
    } else if (view()->exists('voyager::' . $slug . '.browse')) {
      return view('voyager::' . $slug . '.browse', array('dataType' => $dataType, 'dataTypeContent' => $dataTypeContent));
    } else {
      return view('voyager::bread.browse', array('dataType' => $dataType, 'dataTypeContent' => $dataTypeContent));
    }

  }

    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

  public function show(Request $request, $id)
  {
    $slug = $request->segment(2);
    $dataType = DataType::where('slug', '=', $slug)->first();
    eval('$dataTypeContent = ' . $dataType->model_name . '::find(' . $id . ');');
    return view('voyager::bread.read', array('dataType' => $dataType, 'dataTypeContent' => $dataTypeContent));
  }

    //***************************************
    //                ______
    //               |  ____|
    //               | |__
    //               |  __|
    //               | |____
    //               |______|
    //
    //  Edit an item of our Data Type BR(E)AD
    //
    //****************************************

  public function edit(Request $request, $id)
  {
    $slug = $request->segment(2);
    $dataType = DataType::where('slug', '=', $slug)->first();
    eval('$dataTypeContent = ' . $dataType->model_name . '::find(' . $id . ');');


    if(view()->exists('admin.' . $slug . '.edit-add')){
      return view('admin.' . $slug . '.edit-add', array('dataType' => $dataType, 'dataTypeContent' => $dataTypeContent));
    } else if (view()->exists('voyager::' . $slug . '.edit-add')) {
      return view('voyager::' . $slug . '.edit-add', array('dataType' => $dataType, 'dataTypeContent' => $dataTypeContent));
    } else {
      return view('voyager::bread.edit-add', array('dataType' => $dataType, 'dataTypeContent' => $dataTypeContent));
    }


  }

    // POST BR(E)AD
  public function update(Request $request, $id)
  {
    $slug = $request->segment(2);
    $dataType = DataType::where('slug', '=', $slug)->first();
    eval('$data = ' . $dataType->model_name . '::find(' . $id . ');');
    $this->insertUpdateData($request, $slug, $dataType->editRows, $data);
    return redirect('/admin/' .$dataType->slug)->with(array('message' => 'Successfully Updated ' . $dataType->display_name_singular, 'alert-type' => 'success'));
  }

    //***************************************
    //
    //                   /\
    //                  /  \
    //                 / /\ \
    //                / ____ \
    //               /_/    \_\
    //
    //
    // Add a new item of our Data Type BRE(A)D
    //
    //****************************************

  public function create(Request $request)
  {
    $slug = $request->segment(2);
    $dataType = DataType::where('slug', '=', $slug)->first();
    if(view()->exists('admin.' . $slug . '.edit-add')){
      return view('admin.' . $slug . '.edit-add', array('dataType' => $dataType));
    } else if (view()->exists('voyager::' . $slug . '.edit-add')) {
      return view('voyager::' . $slug . '.edit-add', array('dataType' => $dataType));
    } else {
      return view('voyager::bread.edit-add', array('dataType' => $dataType));
    }
  }

    // POST BRE(A)D
  public function store(Request $request)
  {
        //
    $slug = $request->segment(2);
    $dataType = DataType::where('slug', '=', $slug)->first();

    if (function_exists('voyager_add_post')) {
      $url = $request->url();
      voyager_add_post($request);
    }

    eval('$data = new ' . $dataType->model_name . ';');
    $this->insertUpdateData($request, $slug, $dataType->addRows, $data);
    return redirect('/admin/' .$dataType->slug)->with(array('message' => 'Successfully Added New ' . $dataType->display_name_singular, 'alert-type' => 'success'));
  }

    //***************************************
    //                _____
    //               |  __ \
    //               | |  | |
    //               | |  | |
    //               | |__| |
    //               |_____/
    //
    //         Delete an item BREA(D)
    //
    //****************************************

  public function destroy(Request $request, $id)
  {
    $slug = $request->segment(2);
    $dataType = DataType::where('slug', '=', $slug)->first();
    
    eval('$data = ' . $dataType->model_name . '::find(' . $id . ');');

    foreach($dataType->deleteRows as $row) {
      if($row->type == 'image') {
        if(\Storage::exists('/uploads/' . $data->{$row->field})) {
          Storage::delete('/uploads/' . $data->{$row->field});
        }

        $options = json_decode($row->details);

        if(isset($options->thumbnails)) {
          foreach($options->thumbnails as $thumbnail) {
            $ext = explode('.', $data->{$row->field});
            $extension = '.' . $ext[count($ext)-1];

            $path = str_replace( $extension, '', $data->{$row->field});

            $thumb_name = $thumbnail->name;

            if( Storage::exists('/uploads/' . $path. '-' . $thumb_name . $extension) ){
              Storage::delete('/uploads/' . $path. '-' . $thumb_name . $extension);
            }

          }  // end if isset
        } // end if storage
      } // end if row->type
    } // end foreach

    if($data->destroy($id)){
      return redirect('/admin/' . $dataType->slug)->with(array('message' => 'Successfully Deleted ' . $dataType->display_name_singular, 'alert-type' => 'success'));
    }

    return redirect('/admin/' . $dataType->display_name_singular)->with(array('message' => 'Sorry it appears there was a problem deleting this ' . $dataType->display_name_singular, 'alert-type' => 'error'));

  } // end of destroy()

   public function insertUpdateData($request, $slug, $rows, $data){

    foreach($rows as $row){

      $content = $this->getContentBasedOnType($request, $slug, $row);
      if($content === NULL){
        if(isset($data->{$row->field})){
          $content = $data->{$row->field};
        }
        if($row->field == 'password'){
          $content = $data->{$row->field};
        }
      } 

      $data->{$row->field} = $content;
    }

    $data->save();

  }

  public function getContentBasedOnType($request, $slug, $row){
    /********** PASSWORD TYPE **********/
    if($row->type == 'password'){
      $pass_field = $request->input($row->field);
      if(isset($pass_field) && !empty($pass_field)){
        $content = bcrypt($request->input($row->field));
      } else {
       $content = NULL;
     }

      /********** CHECKBOX TYPE **********/
    } else if($row->type == 'checkbox'){
      $content = 0;
      $checkBoxRow = $request->input($row->field);

      if(isset($checkBoxRow)){
        $content = 1;
      }

      /********** FILE TYPE **********/
    } else if($row->type == 'file'){


      /********** IMAGE TYPE **********/
    } else if($row->type == 'image'){


      if ($request->hasFile($row->field)) {

        $storage_disk = 'local';

        $file = $request->file($row->field);
        $filename = str_random(20);

        $path =  $slug . '/' . date('F') . date('Y') . '/';
        $full_path = $path . $filename . '.' . $file->getClientOriginalExtension();

        $options = json_decode($row->details);

        if(isset($options->resize) && isset($options->resize->width) && isset($options->resize->height) ){
          $resize_width = $options->resize->width;
          $resize_height = $options->resize->height;
        } else {
          $resize_width = 1800;
          $resize_height = null;
        }

        $image = Image::make($file)->resize($resize_width, $resize_height, function($constraint){ 
          $constraint->aspectRatio();
          $constraint->upsize();
        })->encode($file->getClientOriginalExtension(), 75);

        Storage::put(config('voyager.storage.subfolder') . $full_path, (string)$image, 'public');

        if(isset($options->thumbnails)){
          foreach($options->thumbnails as $thumbnails){

            if(isset($thumbnails->name) && isset($thumbnails->scale)){
              $scale = intval($thumbnails->scale)/100;
              $thumb_resize_width = $resize_width;
              $thumb_resize_height = $resize_height;
              if($thumb_resize_width != 'null'){
                $thumb_resize_width = $thumb_resize_width*$scale;
              }
              if($thumb_resize_height != 'null'){
                $thumb_resize_height = $thumb_resize_height*$scale;
              }
              $image = Image::make($file)->resize($thumb_resize_width, $thumb_resize_height, function($constraint){ 
                $constraint->aspectRatio(); 
                $constraint->upsize();
              })->encode($file->getClientOriginalExtension(), 75);

              
            } elseif(isset($options->thumbnails) && isset($thumbnails->crop->width) && isset($thumbnails->crop->height)) {
              $crop_width = $thumbnails->crop->width;
              $crop_height = $thumbnails->crop->height;
              $image = Image::make($file)->fit($crop_width, $crop_height)->encode($file->getClientOriginalExtension(), 75);
            }

            Storage::put(config('voyager.storage.subfolder') . $path . $filename . '-' . $thumbnails->name . '.' . $file->getClientOriginalExtension(), (string)$image, 'public');
          }
        }

        $content = $full_path;

      } else {

        $content = NULL;

      }

      /********** ALL OTHER TEXT TYPE **********/
    } else {
      $content = $request->input($row->field);
    }

    return $content;
  }

  public function generate_views(Request $request){
          //$dataType = DataType::where('slug', '=', $slug)->first();
  }
}
