<?php

namespace TCG\Voyager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Setting;
use TCG\Voyager\Models\User;

class Voyager
{
    private static $instance;

    protected $version;
    protected $filesystem;

    public function __construct()
    {
        $this->filesystem = app(Filesystem::class);

        $this->findVersion();
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function setting($key, $default = null)
    {
        $setting = Setting::where('key', '=', $key)->first();
        if (isset($setting->id)) {
            return $setting->value;
        }

        return $default;
    }

    public static function image($file, $default = '')
    {
        if (!empty($file) && Storage::exists(config('voyager.storage.subfolder').$file)) {
            return Storage::url(config('voyager.storage.subfolder').$file);
        }

        return $default;
    }

    public static function routes()
    {
        require __DIR__.'/../routes/voyager.php';
    }

    public static function can($permission)
    {
        // Check if permission exist
        $exist = Permission::where('key', $permission)->first();

        if ($exist) {
            $user = User::find(Auth::id());
            if (!$user->hasPermission($permission)) {
                throw new UnauthorizedHttpException(null);
            }
        }
    }

    public function getVersion()
    {
        return $this->version;
    }

    protected function findVersion()
    {
        if (!is_null($this->version)) {
            return;
        }

        if ($this->filesystem->exists(base_path('composer.lock'))) {
            // Get the composer.lock file
            $file = json_decode(
                $this->filesystem->get(base_path('composer.lock'))
            );

            // Loop through all the packages and get the version of voyager
            foreach ($file->packages as $package) {
                if ($package->name == 'tcg/voyager') {
                    $this->version = $package->version;
                    break;
                }
            }
        }
    }
}
