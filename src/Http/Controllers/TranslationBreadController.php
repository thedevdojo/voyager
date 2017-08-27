<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

class TranslationBreadController extends VoyagerBreadController
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
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $translationModel = get_class(app($dataType->model_name)->first()->translations->first());
        $dataTypeTranslation = Voyager::model('DataType')->where('model_name', '=', $translationModel)->first();
        $dataRows = $dataType->browseRows->merge($dataTypeTranslation->browseRows);
        // Check permission
        Voyager::canOrFail('browse_'.$dataType->name);

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model::select('*');

            $relationships = $this->getRelationships($dataType);

            if ($search->value && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            if ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest(), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->with($relationships)->orderBy('id', 'DESC'), $getter]);
            }

            //Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        $view = 'voyager::translation-bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'search', 'searchable', 'dataRows'));
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

        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // If dataType is users and user owns the profile, skip the permission check
        $skip = $dataType->name === 'users' && $request->user()->id === (int) $id;

        if (!$skip) {
            Voyager::canOrFail('edit_'.$dataType->name);
        }

        $dataTypeContent = app($dataType->model_name)->findOrFail($id);
        $dataTypeContentTranslation = $dataTypeContent->translations->first();

        $dataTypeTranslation = Voyager::model('DataType')
            ->where('model_name', '=', $dataTypeContent->getTranslationModelName())
            ->first();

        // Check if BREAD is Translation
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::translation-bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'dataTypeTranslation',
            'dataTypeContentTranslation'
            )
        );
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
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $formBuilder = $dataType->getFormBuilder();

        // If dataType is users and user owns the profile, skip the permission check
        $skip = $dataType->name === 'users' && $request->user()->id === (int) $id;

        if (!$skip) {
            Voyager::canOrFail('edit_'.$dataType->name);
        }

        $dataTypeContent = app($dataType->model_name)->findOrFail($id);
        $dataTypeContentTranslation = $dataTypeContent->translations->first();

        $dataTypeTranslation = Voyager::model('DataType')
            ->where('model_name', '=', $dataTypeContent->getTranslationModelName())
            ->first();

        // Check if BREAD is Translation
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $dataTypeRows = $dataType->editRows;
        $dataTypeRowsTranslation = $dataTypeTranslation->editRows;
        $dataTypeRows = $dataTypeRows->merge($dataTypeRowsTranslation);

        $view = 'voyager::translation-bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'dataTypeRows',
            'formBuilder'
            )
        );
    }

    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // If dataType is users and user owns the profile, skip the permission check
        $skip = $dataType->name === 'users' && $request->user()->id === (int) $id;
        if (!$skip) {
            Voyager::canOrFail('edit_'.$dataType->name);
        }

        //Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        $dataTypeContent = new $dataType->model_name;
        $translation_model_name = $dataTypeContent->getTranslationModelName();

        $dataTypeTranslation = Voyager::model('DataType')
            ->where('model_name', '=', $translation_model_name)
            ->first();

        //Validate translation fields with ajax
        $val = $this->validateBread($request->all(), $dataTypeTranslation->editRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->ajax()) {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

            $rows = $dataType->editRows->merge($dataTypeTranslation->editRows);
            $this->insertUpdateData($request, $slug, $rows, $data);

            return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager.generic.successfully_updated')." {$dataType->display_name_singular}",
                'alert-type' => 'success',
                ]);
        }
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
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $formBuilder = $dataType->getFormBuilder();

        // Check permission
        Voyager::canOrFail('add_'.$dataType->name);

        $dataTypeContent = new $dataType->model_name;
        $translation_model_name = $dataTypeContent->getTranslationModelName();

        $dataTypeContentTranslation = new $translation_model_name;

        $dataTypeTranslation = Voyager::model('DataType')
            ->where('model_name', '=', $translation_model_name)
            ->first();

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $dataTypeRows = $dataType->addRows;
        $dataTypeRowsTranslation = $dataTypeTranslation->addRows;
        $dataTypeRows = $dataTypeRows->merge($dataTypeRowsTranslation);

        $view = 'voyager::translation-bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'dataTypeRows',
            'formBuilder'
            )
        );
    }

    // POST BRE(A)D
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('add_'.$dataType->name);

        //Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        $dataTypeContent = new $dataType->model_name;
        $translation_model_name = $dataTypeContent->getTranslationModelName();

        $dataTypeTranslation = Voyager::model('DataType')
            ->where('model_name', '=', $translation_model_name)
            ->first();

        //Validate translation fields with ajax
        $val = $this->validateBread($request->all(), $dataTypeTranslation->addRows);

        if ($val->fails()) {
            return response()->json(['errors' => $val->messages()]);
        }

        if (!$request->ajax()) {
            $rows = $dataType->editRows->merge($dataTypeTranslation->editRows);
            $data = $this->insertUpdateData($request, $slug, $rows, new $dataType->model_name());

            return redirect()
                ->route("voyager.{$dataType->slug}.index")
                ->with([
                        'message'    => __('voyager.generic.successfully_added_new')." {$dataType->display_name_singular}",
                        'alert-type' => 'success',
                    ]);
        }
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
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        Voyager::canOrFail('delete_'.$dataType->name);

        $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);

        // Delete Translations, if present
        if (is_bread_translatable($data)) {
            $dataTypeContent = app($dataType->model_name)->findOrFail($id);
            $dataTypeContent->translations()->delete();
        }

        // Delete Images
        $this->deleteBreadImages($data, $dataType->deleteRows->where('type', 'image'));

        // Delete Files
        foreach ($dataType->deleteRows->where('type', 'file') as $row) {
            foreach (json_decode($data->{$row->field}) as $file) {
                $this->deleteFileIfExists($file->download_link);
            }
        }

        $data = $data->destroy($id)
            ? [
                'message'    => __('voyager.generic.successfully_deleted')." {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]
            : [
                'message'    => __('voyager.generic.error_deleting')." {$dataType->display_name_singular}",
                'alert-type' => 'error',
            ];

        return redirect()->route("voyager.{$dataType->slug}.index")->with($data);
    }

    /**
     * Delete all images related to a BREAD item.
     *
     * @param \Illuminate\Database\Eloquent\Model $data
     * @param \Illuminate\Database\Eloquent\Model $rows
     *
     * @return void
     */
    public function deleteBreadImages($data, $rows)
    {
        foreach ($rows as $row) {
            $this->deleteFileIfExists($data->{$row->field});

            $options = json_decode($row->details);

            if (isset($options->thumbnails)) {
                foreach ($options->thumbnails as $thumbnail) {
                    $ext = explode('.', $data->{$row->field});
                    $extension = '.'.$ext[count($ext) - 1];

                    $path = str_replace($extension, '', $data->{$row->field});

                    $thumb_name = $thumbnail->name;

                    $this->deleteFileIfExists($path.'-'.$thumb_name.$extension);
                }
            }
        }
    }
}
