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
        $webrtc = [];

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
        } elseif (Auth::check()) {
            if (Config::get('app.env') == 'local') {
                Cache::forget('permissions_user_' . Auth::user()->id);
            }
//            if (!Cache::has('permissions_user_' . Auth::user()->id)) {
//                Cache::rememberForever('permissions_user_' . Auth::user()->id, function () {
//                    return app('userRoles')->getRoles(Auth::user());
//                });
//            }
//            $permissions = Cache::get('permissions_user_' . Auth::user()->id);

            if (!empty(Auth::user()->extension_id)) {
                if (Config::get('app.env') == 'local') {
                    Cache::forget('user_webrtc_' . Auth::user()->id);
                }
                if (!Cache::has('user_webrtc_' . Auth::user()->id)) {
                    Cache::rememberForever('user_webrtc_' . Auth::user()->id, function () {
                        return [
                            'server_ip' => env('PUSHER_HOST'),
//                            'server_port' => Helper::getExtensionPort(Auth::user()->extension),
                            'server_domain' => str_replace(['http://', 'https://'], '', env('APP_URL')),
                            'user' => Auth::user()->company->tenant . Auth::user()->extension->number,
//                            'uri' => 'sip:'.Auth::user()->company->tenant . Auth::user()->extension->number.'@'.env('PUSHER_HOST').':'.Helper::getExtensionPort(Auth::user()->extension),
                            'password' => Auth::user()->extension->password,
                            'display_name' => Auth::user()->extension->name
                        ];
                    });
                }
                $webrtc = Cache::get('user_webrtc_' . Auth::user()->id);
            }

        }

        if (Config::get('app.env') == 'local') {
            Cache::forget('languageMessages');
        }

        if (!Cache::has('languageMessages')) {
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
        }

        $languageMessages = Cache::get('languageMessages');

        if (Session::has('locale')) {
            $locale = Session::get('locale');
        } else {
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        }

        if (!in_array($locale, $languages)) {
            $locale = Config::get('app.locale');
        }

        App::setLocale($locale);


        if(Auth::check()){

//        $webrtc = [
//            'server_ip' => '45.10.253.214',
//        //    'server_port' => Helper::getExtensionPort(Auth::user()->extension),
//            'server_domain' => 'santralbeta.telsam.com.tr',
//            'user' => Auth::user()->company->tenant . Auth::user()->extension->number,
//           // 'uri' => 'sip:'.Auth::user()->company->tenant . Auth::user()->extension->number.'@45.10.253.214:'.Helper::getExtensionPort(Auth::user()->extension),
//            'password' => Auth::user()->extension->password,
//            'display_name' => Auth::user()->extension->name
//        ];

        }

        return array_merge(parent::share($request), [
            'appName' => Config::get('app.name'),
            'appVersion' => Config::get('app.version'),
            'currentLanguage' => App::getLocale(),
            'languages' => $languages,
            'languageMessages' => $languageMessages,
            'loginUser' => Auth::user(),
            'permissions' => $permissions,
            'user_email_md5' => (Auth::check()) ? md5(mb_strtolower(trim(Auth::user()->email))) : '',
            'webrtc' => $webrtc,
            'flash' => [
                'error' => fn() => $request->session()->get('error'),
                'message' => fn() => $request->session()->get('message')
            ]
        ]);
    }
}
