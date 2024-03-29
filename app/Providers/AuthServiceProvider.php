<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Libraries\UserRole;
use App\Models\Admin;
use App\Models\AdminRoleGroup;
use App\Models\Archive;
use App\Models\Company;
use App\Models\Group;
use App\Models\Manager;
use App\Models\ManagerRoleGroup;
use App\Models\Package;
use App\Models\Person;
use App\Models\User;
use App\Models\UserRoleGroup;
use App\Policies\Admin\AdminPolicy;
use \App\Policies\Admin\AdminRoleGroupPolicy;
use App\Policies\Admin\CompanyPolicy;
use App\Policies\Admin\ManagerPolicy as AdminManagerPolicy;
use App\Policies\Admin\PackagePolicy;
use App\Policies\Admin\UserPolicy as AdminUserPolicy;
use App\Policies\Admin\ArchivePolicy as AdminArchivePolicy;
use App\Policies\Manager\ManagerPolicy;
use App\Policies\Manager\ManagerRoleGroupPolicy;
use App\Policies\Manager\UserPolicy as ManagerUserPolicy;
use App\Policies\Manager\UserRoleGroupPolicy as ManagerUserRoleGroupPolicy;
use App\Policies\Manager\ArchivePolicy as ManagerArchivePolicy;
use App\Policies\User\ArchivePolicy;
use App\Policies\User\GroupPolicy;
use App\Policies\User\PersonPolicy;
use App\Policies\User\UserPolicy;
use App\Policies\User\UserRoleGroupPolicy;
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
            && !Str::startsWith($this->app->request->getRequestUri(), '/Admin/ManagerScreen/')
        ) {
            $this->policies[AdminRoleGroup::class] = AdminRoleGroupPolicy::class;
            $this->policies[Admin::class] = AdminPolicy::class;
            $this->policies[Company::class] = CompanyPolicy::class;
            $this->policies[Manager::class] = AdminManagerPolicy::class;
            $this->policies[User::class] = AdminUserPolicy::class;
            $this->policies[Archive::class] = AdminArchivePolicy::class;
            $this->policies[Package::class] = PackagePolicy::class;
        } elseif (
            (Str::startsWith($this->app->request->getRequestUri(), '/Manager')
                && !Str::startsWith($this->app->request->getRequestUri(), '/Manager/UserScreen/')
            ) || Str::startsWith($this->app->request->getRequestUri(), '/Admin/ManagerScreen/')
        ) {
            $this->policies[ManagerRoleGroup::class] = ManagerRoleGroupPolicy::class;
            $this->policies[Manager::class] = ManagerPolicy::class;
            $this->policies[User::class] = ManagerUserPolicy::class;
            $this->policies[UserRoleGroup::class] = ManagerUserRoleGroupPolicy::class;
            $this->policies[Archive::class] = ManagerArchivePolicy::class;
        } else {
            $this->policies[UserRoleGroup::class] = UserRoleGroupPolicy::class;
            $this->policies[User::class] = UserPolicy::class;
            $this->policies[Group::class] = GroupPolicy::class;
            $this->policies[Person::class] = PersonPolicy::class;
            $this->policies[Archive::class] = ArchivePolicy::class;
        }

        $this->registerPolicies();
        $this->initUserRoles();
    }
}
