<?php

namespace TCG\Voyager\Controllers;

use App\Http\Controllers\Controller;
use \Redirect as Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VoyagerMediaController extends Controller {

    private $directory = "";

    public function __construct(){
        $this->filesystem = config('filesystems.default');
        if($this->filesystem == 'local'){
            $this->directory = 'public';
        } else if($this->filesystem == 's3'){
            $this->directory = '';
        }
    }

    public function index()
    {
        return view('voyager::media.index');
    }
    
    public function files(Request $request)
    {
        $folder = $request->folder;
        if ($folder == '/') {
            $folder = '';
        }
        $dir = $this->directory . $folder;

        $response = $this->getFiles($dir);

        return response()->json(array(
            "name" => "files",
            "type" => "folder",
            "path" => $dir,
            "folder" => $folder,
            "items" => $response,
            "last_modified" => 'asdf'
        ));
    }

    private function getFiles($dir)
    {

        $files = array();
        $storage_files = Storage::files($dir);
        $storage_folders = Storage::directories($dir);

        foreach($storage_files as $file){
            //preg_match('/\/(\d{6})$/', $file, $filename);
            if(strpos($file, '/') > 1){
                $fname = str_replace('/', '', strrchr( $file, '/'));
            } else {
                $fname = $file;
            }
            $files[] = array(
                "name" => $fname,
                "type" => Storage::mimeType($file),
                "path" => Storage::disk(config('filesystem.default'))->url($file),
                "size" => Storage::size($file),
                "last_modified" => Storage::lastModified($file)
            );
        }

        foreach($storage_folders as $folder){
            
            if(strpos($folder, '/') > 1){
                $fname = str_replace('/', '', strrchr( $folder, '/'));
            } else {
               $fname = $folder;
            }

            $files[] = array(
                "name" => $fname,
                "type" => 'folder',
                "path" => Storage::disk(config('filesystem.default'))->url($folder),
                "items" => '',
                "last_modified" => ''
            );
        }

        return $files;
    }

    // New Folder with 5.3

    public function new_folder(Request $request)
    {
        $new_folder = $request->new_folder;
        $success = false;
        $error = '';
        

        if (Storage::exists($new_folder)) {
            $error = 'Sorry that folder already exists, please delete that folder if you wish to re-create it';
        } else {
            if (Storage::makeDirectory($new_folder)) {
                $success = true;
            } else {
                $error = 'Sorry something seems to have gone wrong with creating the directory, please check your permissions';
            }
        }

        return array('success' => $success, 'error' => $error);
    }


    // Delete File or Folder with 5.3

    public function delete_file_folder(Request $request)
    {
        $folder_location = $request->folder_location;
        $file_folder = $request->file_folder;
        $type = $request->type;
        $success = true;
        $error = '';

        if(is_array($folder_location)){
            $folder_location = rtrim(join('/', $folder_location), '/');
        }

        $location = $this->directory . '/' . $folder_location;

        $file_folder = $location . '/' . $file_folder;

        if ($type == 'folder') {
            if (!Storage::deleteDirectory($file_folder)) {
                $error = 'Sorry something seems to have gone wrong when deleting this folder, please check your permissions';
                $success = false;
            }
        } else {
            if (!Storage::delete($file_folder)) {
                $error = 'Sorry something seems to have gone wrong deleting this file, please check your permissions';
                $success = false;
            }
        }

        return array('success' => $success, 'error' => $error);
    }

    // GET ALL DIRECTORIES Working with Laravel 5.3

    public function get_all_dirs(Request $request)
    {
        $folder_location = $request->folder_location;

        if(is_array($folder_location)){
            $folder_location = rtrim(join('/', $folder_location), '/');
        }

        $location = $this->directory . '/' . $folder_location;

        $directories = str_replace($location, '', Storage::directories($location));

        return response()->json($directories);
    }

    // NEEDS TESTING

    public function move_file(Request $request)
    {
        $source = $request->source;
        $destination = $request->destination;
        $folder_location = $request->folder_location;
        $success = false;
        $error = '';
        
        if(is_array($folder_location)){
            $folder_location = rtrim(join('/', $folder_location), '/');
        }

        $location = $this->directory . '/' . $folder_location;

        $source = $location . '/' . $source;
        if(strpos($destination, '/../') !== false){
            $destination = $this->directory . '/' . dirname($folder_location) . '/' . str_replace('/../', '', $destination);
        } else {
            $destination = $location . '/' . $destination;
        }

        if (!file_exists($destination)) {
            if (Storage::move($source, $destination)) {
                $success = true;
            } else {
                $error = 'Sorry there seems to be a problem moving that file/folder, please make sure you have the correct permissions.';
            }
        } else {
            $error = 'Sorry there is already a file/folder with that existing name in that folder.';
        }

        return array('success' => $success, 'error' => $error);
    }

    // RENAME FILE WORKING with 5.3

    public function rename_file(Request $request)
    {
        $folder_location = $request->folder_location;
        $filename = $request->filename;
        $new_filename = $request->new_filename;
        $success = false;
        $error = false;
        
        if(is_array($folder_location)){
            $folder_location = rtrim(join('/', $folder_location), '/');
        }

        $location = $this->directory . '/' . $folder_location;

        if (!Storage::exists($location . '/' . $new_filename)) {
            if (Storage::move( $location . '/' . $filename, $location . '/' . $new_filename )) {
                $success = true;
            } else {
                $error = 'Sorry there seems to be a problem moving that file/folder, please make sure you have the correct permissions.';
            }
        } else {
            $error = 'File or Folder may already exist with that name. Please choose another name or delete the other file.';
        }

        return array('success' => $success, 'error' => $error);
    }

    // Upload Working with 5.3

    public function upload(Request $request)
    {
        try {
            $path = $request->file->store($request->upload_path);
            $success = true;
            $message = 'Successfully uploaded new file!';
        } catch (Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return response()->json(array('success' => $success, 'message' => $message));
    }

}

