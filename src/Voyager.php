<?php

namespace TCG\Voyager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TCG\Voyager\FormFields\HandlerInterface;
use TCG\Voyager\FormFields\After\HandlerInterface as AfterHandlerInterface;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Setting;
use TCG\Voyager\Models\User;

class Voyager
{
    protected $version;
    protected $filesystem;

    protected $alerts = [];

    protected $alertsCollected = false;

    protected $formFields = [];
    protected $afterFormFields = [];

    public function __construct()
    {
        $this->filesystem = app(Filesystem::class);

        $this->findVersion();
    }

    public function formField($row, $dateType, $dataTypeContent)
    {
        $formField = $this->formFields[$row->type];

        return $formField->handle($row, $dateType, $dataTypeContent);
    }

    public function afterFormFields($row, $dataType, $dataTypeContent)
    {
        $options = json_decode($row->details);

        return collect($this->afterFormFields)->filter(function ($after) use ($row, $dataType, $dataTypeContent, $options) {
            return $after->visible($row, $dataType, $dataTypeContent, $options);
        });
    }

    public function addFormField($handler)
    {
        if (!$handler instanceof HandlerInterface) {
            $handler = app($handler);
        }

        $this->formFields[$handler->getCodename()] = $handler;
    }

    public function addAfterFormField($handler)
    {
        if (!$handler instanceof AfterHandlerInterface) {
            $handler = app($handler);
        }

        $this->afterFormFields[$handler->getCodename()] = $handler;
    }

    public function formFields()
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver", 'mysql');

        return collect($this->formFields)->filter(function ($after) use ($driver) {
            return $after->supports($driver);
        });
    }

    public function setting($key, $default = null)
    {
        $setting = Setting::where('key', '=', $key)->first();

        if (isset($setting->id)) {
            return $setting->value;
        }

        return $default;
    }

    public function image($file, $default = '')
    {
        if (!empty($file) && Storage::exists(config('voyager.storage.subfolder').$file)) {
            return Storage::url(config('voyager.storage.subfolder').$file);
        }

        return $default;
    }

    public function routes()
    {
        require __DIR__.'/../routes/voyager.php';
    }

    public function can($permission)
    {
        // Check if permission exist
        $exist = Permission::where('key', $permission)->first();

        if ($exist) {
            $user = User::find(Auth::id());

            if ($user == null) {
                return false;
            }

            if (!$user->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function canOrFail($permission)
    {
        if (!$this->can($permission)) {
            throw new UnauthorizedHttpException(null);
        }

        return true;
    }

    public function canOrAbort($permission, $statusCode = 403)
    {
        if (!$this->can($permission)) {
            return abort($statusCode);
        }

        return true;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function addAlert(Alert $alert)
    {
        $this->alerts[] = $alert;
    }

    public function alerts()
    {
        if (!$this->alertsCollected) {
            event('voyager.alerts.collecting');

            $this->alertsCollected = true;
        }

        return $this->alerts;
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
