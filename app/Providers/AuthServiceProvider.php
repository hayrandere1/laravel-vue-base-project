<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Libraries\UserRole;
use App\Models\Admin;
use App\Models\AdminRoleGroup;
use App\Models\Company;
use App\Policies\Admin\AdminPolicy;
use \App\Policies\Admin\AdminRoleGroupPolicy;
use App\Policies\Admin\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Str;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    public function initUserRoles()
    {
        $this->app->singleton('userRoles', UserRole::class);
    }

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        if (
            Str::startsWith($this->app->request->getRequestUri(), '/Admin')
            && !Str::startsWith($this->app->request->getRequestUri(), '/Admin/UserScreen/')
        ) {
            $this->policies[AdminRoleGroup::class] = AdminRoleGroupPolicy::class;
            $this->policies[Admin::class] = AdminPolicy::class;
            $this->policies[Company::class] = CompanyPolicy::class;
        } else if (
            Str::startsWith($this->app->request->getRequestUri(), '/Manager')
            && !Str::startsWith($this->app->request->getRequestUri(), '/Manager/UserScreen/')
        ) {

        } else {

        }

        $this->registerPolicies();
        $this->initUserRoles();
    }
}
