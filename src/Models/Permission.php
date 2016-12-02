<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['key', 'table_name'];

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public static function generateFor($table_name)
    {
        Permission::firstOrCreate(['key' => 'browse_'.$table_name, 'table_name' => $table_name]);
        Permission::firstOrCreate(['key' => 'read_'.$table_name, 'table_name' => $table_name]);
        Permission::firstOrCreate(['key' => 'edit_'.$table_name, 'table_name' => $table_name]);
        Permission::firstOrCreate(['key' => 'add_'.$table_name, 'table_name' => $table_name]);
        Permission::firstOrCreate(['key' => 'delete_'.$table_name, 'table_name' => $table_name]);
    }

    public static function removeFrom($table_name)
    {
        Permission::where(['table_name' => $table_name])->delete();
    }
}
