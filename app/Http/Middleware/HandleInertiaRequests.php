<?php

namespace App\Http\Middleware;

use App\Libraries\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        $languages = Config::get('app.languages', ['en']);
        $permissions = [];

        $user = Auth::user();
        $session = \App\Models\Session::where('id', Session::getId())->first();
        if (!empty($user)) {
            $guard = Auth::guard()->name;
            if ($session->user_type == 'guest') {
                \App\Models\Session::where('id', Session::getId())->update([
                    'user_type' => $guard,
                ]);
            }
        }
        if (empty($session->created_at)) {
            \App\Models\Session::where('id', Session::getId())->update([
                'created_at' => now(),
            ]);
        }

        if (Auth::guard('admin')->check() && !is_null(Auth::user('admin'))) {
            if (Config::get('app.env') == 'local') {
                Cache::forget('permissions_admin_' . Auth::user('admin')->id);
            }
            if (!Cache::has('permissions_admin_' . Auth::user('admin')->id)) {
                Cache::rememberForever('permissions_admin_' . Auth::user('admin')->id, function () {
                    return app('userRoles')->getRoles(Auth::user('admin'));
                });
            }
            $permissions = Cache::get('permissions_admin_' . Auth::user('admin')->id);
        } elseif (Auth::guard('manager')->check() && !is_null(Auth::user('manager'))) {
            if (Config::get('app.env') == 'local') {
                Cache::forget('permissions_manager_' . Auth::user('manager')->id);
            }
            if (!Cache::has('permissions_manager_' . Auth::user('manager')->id)) {
                Cache::rememberForever('permissions_manager_' . Auth::user('manager')->id, function () {
                    return app('userRoles')->getRoles(Auth::user('manager'));
                });
            }
            $permissions = Cache::get('permissions_manager_' . Auth::user('manager')->id);
        } elseif (Auth::check()) {
            if (Config::get('app.env') == 'local') {
                Cache::forget('permissions_user_' . Auth::user()->id);
            }
            if (!Cache::has('permissions_user_' . Auth::user()->id)) {
                Cache::rememberForever('permissions_user_' . Auth::user()->id, function () {
                    return app('userRoles')->getRoles(Auth::user());
                });
            }
            $permissions = Cache::get('permissions_user_' . Auth::user()->id);

        }

        if (Config::get('app.env') == 'local') {
            Cache::forget('languageMessages');
        }

        /*   if (!Cache::has('languageMessages')) {
               $languages = Config::get('app.languages', ['en']);
               Cache::rememberForever('languageMessages', function () use ($languages) {
                   $response = '{';
                   $i = 0;
                   foreach ($languages as $lang) {
                       if ($i > 0) {
                           $response .= ',';
                       }
                       if (file_exists(lang_path($lang . '.json'))) {
                           $fileContent = file_get_contents(lang_path($lang . '.json'));
                       } else {
                           $fileContent = '{}';
                       }
                       $response .= '"' . $lang . "\":" . $fileContent;
                       $i++;
                   }
                   $response .= '}';
                   return $response;
               });
           }*/

        // $languageMessages = Cache::get('languageMessages');

        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } else {
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        }

        if (!in_array($locale, $languages)) {
            $locale = Config::get('app.locale');
        }

        App::setLocale($locale);

        //'languageMessages' => $languageMessages,
        return array_merge(parent::share($request), [
            'appName' => Config::get('app.name'),
            'appVersion' => Config::get('app.version'),
            'currentLanguage' => App::getLocale(),
            'languages' => $languages,
            'loginUser' => Auth::user(),
            'permissions' => $permissions,
            'user_email_md5' => (Auth::check()) ? md5(mb_strtolower(trim(Auth::user()->email))) : '',
            'flash' => [
                'error' => fn() => $request->session()->get('error'),
                'message' => fn() => $request->session()->get('message')
            ]
        ]);
    }
}
