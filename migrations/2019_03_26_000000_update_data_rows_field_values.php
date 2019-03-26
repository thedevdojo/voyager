<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use TCG\Voyager\Models\DataRow;
use TCG\Voyager\Models\DataType;

class UpdateDataRowsFieldValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $records = DataRow::where('type', 'relationship')->get();
        foreach ($records as $rec) {
            $dataType = DataType::find($rec->data_type_id);
            $field = Str::singular($dataType->name).'_'.$rec->details->type.'_'.Str::singular($rec->details->table).'_relationship_'.$rec->details->column;
            
            $a = $rec->details;
            $a->_fallback_field = $rec->field;
            
            $rec->details = $a;
            $rec->field = strtolower($field);
            $rec->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $records = DataRow::where('type', 'relationship')->get();
        foreach ($records as $rec) {
       
            if (property_exists($rec->details, '_fallback_field')) {
                $field = $rec->details->_fallback_field;
            
                $a = $rec->details;
                unset($a->_fallback_field);

                $rec->details = $a;
                $rec->field = $field;
    
                $rec->save();
            }
        }
    }
}
