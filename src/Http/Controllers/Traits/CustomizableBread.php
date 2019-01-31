<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Http\Request;

trait CustomizableBread
{
    /**
     * Returns the base query used to get the index records
     */
    protected function getQueryForIndex(Request $request, $dataType)
    {
        return app($dataType->model_name)->select('*');
    }

    /**
     * Returns the model used to show a record
     */
    protected function getDataForShow(Request $request, $dataType, $id)
    {
        return app($dataType->model_name)->findOrFail($id);
    }

    /**
     * Returns the model used to display the edit form
     */
    protected function getDataForEdit(Request $request, $dataType, $id)
    {
        return app($dataType->model_name)->findOrFail($id);
    }

    /**
     * Returns the model that will be merged with input when updating a record
     */
    protected function getDataForUpdate(Request $request, $dataType, $id)
    {
        return app($dataType->model_name)->findOrFail($id);
    }

    /**
     * Returns a new model instance used to display the create form
     */
    protected function getDataForCreate(Request $request, $dataType)
    {
        return app($dataType->model_name);
    }

    /**
     * Returns a new model instance that will be merge with inputs when creating a record
     */
    protected function getDataForStore(Request $request, $dataType)
    {
        return app($dataType->model_name);
    }

    /**
     * Returns a query used to get the records to be deleted
     */
    protected function getQueryForDestroy(Request $request, $dataType, $ids)
    {
        return app($dataType->model_name)->whereIn('id', $ids);
    }

    /**
     * Returns the query used to order records
     */
    protected function getQueryForOrder(Request $request, $dataType)
    {
        return app($dataType->model_name)->orderBy($dataType->order_column, $dataType->order_direction);
    }

    /**
     * Returns the route to which to redirect after update/store/delete
     */
    protected function getRedirectRoute(Request $request, $dataType, $data = null)
    {
        return route("voyager.{$dataType->slug}.index");
    }
}
