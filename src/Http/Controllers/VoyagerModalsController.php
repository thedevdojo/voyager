<?php
namespace TCG\Voyager\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataDeleted;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Events\BreadImagesDeleted;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\Traits\BreadRelationshipParser;
class VoyagerModalsController extends Controller
{
    use BreadRelationshipParser;
    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request, $table)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->whereName($table)->first();
        // Check permission
        $this->authorize('add', app($dataType->model_name));
        $dataTypeContent = (strlen($dataType->model_name) != 0)
                            ? new $dataType->model_name()
                            : false;
        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }
        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');
        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);
        $view = 'voyager::modals.create';
        if (view()->exists("voyager::$slug.create")) {
            $view = "voyager::$slug.create";
        }
        $rand = rand();
        $html = view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'rand'))->render();
        return response()->json([
            'success' => true,
            'error' => false,
            'message' => 'null',
            'html' => $html,
        ]);
    }
}