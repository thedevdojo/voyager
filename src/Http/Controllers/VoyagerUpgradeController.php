<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VoyagerUpgradeController extends Controller
{
    public function index()
    {
        $upgraded = $this->upgrade_v0_10_6();

        if ($upgraded) {
            return redirect()->route('voyager.dashboard')->with(['message' => 'Database Schema has been Updated.', 'alert-type' => 'success']);
        } else {
            return redirect()->route('voyager.dashboard');
        }
    }

    private function upgrade_v0_10_6()
    {
        if (!Schema::hasColumn('data_types', 'server_side')) {
            Schema::table('data_types', function (Blueprint $table) {
                $table->tinyInteger('server_side')->default(0)->after('generate_permissions');
            });

            return true;
        }

        return false;
    }
}
