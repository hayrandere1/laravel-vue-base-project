<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';
    public const MANAGER_HOME = '/Manager';
    public const ADMIN_HOME = '/Admin';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {

        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->name('user.')
                ->group(base_path('routes/user.php'));

            Route::middleware('web')
                ->prefix('Manager')
                ->name('manager.')
                ->group(base_path('routes/manager.php'));

            Route::middleware('web')
                ->prefix('Admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

//            if(str_starts_with(\request()->getRequestUri(), '/Admin/UserScreen/')){
//                Route::middleware(['web','admin_user_login'])
//                    ->prefix('Admin/UserScreen/'.\request()->segments()[2])
//                    ->name('user.')
//                    ->group(base_path('routes/user.php'));
//            }

        });
    }
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
