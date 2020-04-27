<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use TCG\Voyager\Facades\Plugins as PluginsFacade;
use TCG\Voyager\Facades\Voyager as VoyagerFacade;
use TCG\Voyager\Plugins\AuthenticationPlugin;

abstract class Controller extends BaseController
{
    public function authorize($ability, $arguments = [])
    {
        return $this->getAuthorizationPlugin()->each(function ($plugin) use ($ability, $arguments) {
            $plugin->authorize($ability, $arguments);
        });
    }

    protected function getAuthorizationPlugin()
    {
        return PluginsFacade::getPluginsByType('authorization');
    }

    protected function getAuthenticationPlugin()
    {
        return PluginsFacade::getPluginByType('authentication', AuthenticationPlugin::class);
    }

    protected function validateData($formfields, $data): array
    {
        $errors = [];

        $formfields->each(function ($formfield) use (&$errors, $data) {
            if (empty($formfield->validation)) {
                return;
            }
            $value = $data[$formfield->column->column] ?? '';
            if ($formfield->translatable && is_array($value)) {
                // TODO: We could validate ALL locales here. But mostly, this doesn't make sense (Let user select?)
                $value = $value[VoyagerFacade::getLocale()] ?? $value[VoyagerFacade::getFallbackLocale()];
            }
            foreach ($formfield->validation as $rule) {
                if ($rule->rule == '') {
                    continue;
                }
                $validator = Validator::make(['col' => $value], ['col' => $rule->rule]);

                if ($validator->fails()) {
                    $message = $rule->message;
                    if (is_object($message)) {
                        $message = $message->{VoyagerFacade::getLocale()} ?? $message->{VoyagerFacade::getFallbackLocale()} ?? '';
                    } elseif (is_array($message)) {
                        $message = $message[VoyagerFacade::getLocale()] ?? $message[VoyagerFacade::getFallbackLocale()] ?? '';
                    }
                    $errors[$formfield->column->column][] = $message;
                }
            }
        });

        return $errors;
    }

    protected function getBread(Request $request)
    {
        return $request->route()->getAction()['bread'] ?? abort(404);
    }

    protected function getLayoutForAction($bread, $action)
    {
        if ($action == 'browse') {
            return $bread->layouts->where('type', 'list')->first();
        }

        return $bread->layouts->where('type', 'view')->first();
    }
}
