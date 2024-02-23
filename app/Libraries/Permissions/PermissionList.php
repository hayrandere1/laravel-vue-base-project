<?php

namespace App\Libraries\Permissions;
class PermissionList
{
    public static function getUserRoleList()
    {
        $roles = [
            'roles.userRoleGroupRoles' => [
                'user.user_role_group.index' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'index',
                    'model' => null
                ],
                'user.user_role_group.create' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'create',
                    'model' => null
                ],
                'user.user_role_group.edit' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'edit',
                    'model' => null
                ],
                'user.user_role_group.destroy' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'destroy',
                    'model' => null
                ],
                'user.user_role_group.show' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'show',
                    'model' => null
                ],
                'user.user_role_group.download' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'download',
                    'model' => null
                ],
            ],
            'roles.userRoles' => [
                'user.user.index' => [
                    'controller' => 'UserController',
                    'action' => 'index',
                    'model' => null],
                'user.user.create' => [
                    'controller' => 'UserController',
                    'action' => 'create',
                    'model' => null],
                'user.user.edit' => [
                    'controller' => 'UserController',
                    'action' => 'edit',
                    'model' => null],
                'user.user.destroy' => [
                    'controller' => 'UserController',
                    'action' => 'destroy',
                    'model' => null],
                'user.user.show' => [
                    'controller' => 'UserController',
                    'action' => 'show',
                    'model' => null],
                'user.user.download' => [
                    'controller' => 'UserController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.groupRoles' => [
                'user.group.index' => [
                    'controller' => 'GroupController',
                    'action' => 'index',
                    'model' => null],
                'user.group.create' => [
                    'controller' => 'GroupController',
                    'action' => 'create',
                    'model' => null],
                'user.group.edit' => [
                    'controller' => 'GroupController',
                    'action' => 'edit',
                    'model' => null],
                'user.group.destroy' => [
                    'controller' => 'GroupController',
                    'action' => 'destroy',
                    'model' => null],
                'user.group.show' => [
                    'controller' => 'GroupController',
                    'action' => 'show',
                    'model' => null],
                'user.group.download' => [
                    'controller' => 'GroupController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.personRoles' => [
                'user.person.index' => [
                    'controller' => 'PersonController',
                    'action' => 'index',
                    'model' => ['group']],
                'user.person.create' => [
                    'controller' => 'PersonController',
                    'action' => 'create',
                    'model' => ['group']],
                'user.person.edit' => [
                    'controller' => 'PersonController',
                    'action' => 'edit',
                    'model' => ['group']],
                'user.person.destroy' => [
                    'controller' => 'PersonController',
                    'action' => 'destroy',
                    'model' => ['group']],
                'user.person.show' => [
                    'controller' => 'PersonController',
                    'action' => 'show',
                    'model' => ['group']],
                'user.person.download' => [
                    'controller' => 'PersonController',
                    'action' => 'download',
                    'model' => ['group']],
            ],
            'roles.archiveRoles' => [
                'user.archive.index' => [
                    'controller' => 'ArchiveController',
                    'action' => 'index',
                    'model' => null],
                'user.archive.destroy' => [
                    'controller' => 'ArchiveController',
                    'action' => 'destroy',
                    'model' => null],
                'user.archive.download' => [
                    'controller' => 'ArchiveController',
                    'action' => 'download',
                    'model' => null],
            ],
        ];
        return $roles;
    }

    public static function getManagerRoleList()
    {
        $roles = [
            'roles.managerRoleGroupRoles' => [
                'manager.manager_role_group.index' => [
                    'controller' => 'ManagerRoleGroupController',
                    'action' => 'index',
                    'model' => null
                ],
                'manager.manager_role_group.create' => [
                    'controller' => 'ManagerRoleGroupController',
                    'action' => 'create',
                    'model' => null
                ],
                'manager.manager_role_group.edit' => [
                    'controller' => 'ManagerRoleGroupController',
                    'action' => 'edit',
                    'model' => null
                ],
                'manager.manager_role_group.destroy' => [
                    'controller' => 'ManagerRoleGroupController',
                    'action' => 'destroy',
                    'model' => null
                ],
                'manager.manager_role_group.show' => [
                    'controller' => 'ManagerRoleGroupController',
                    'action' => 'show',
                    'model' => null
                ],
                'manager.manager_role_group.download' => [
                    'controller' => 'ManagerRoleGroupController',
                    'action' => 'download',
                    'model' => null
                ],
            ],
            'roles.managerRoles' => [
                'manager.manager.index' => [
                    'controller' => 'ManagerController',
                    'action' => 'index',
                    'model' => null],
                'manager.manager.create' => [
                    'controller' => 'ManagerController',
                    'action' => 'create',
                    'model' => null],
                'manager.manager.edit' => [
                    'controller' => 'ManagerController',
                    'action' => 'edit',
                    'model' => null],
                'manager.manager.destroy' => [
                    'controller' => 'ManagerController',
                    'action' => 'destroy',
                    'model' => null],
                'manager.manager.show' => [
                    'controller' => 'ManagerController',
                    'action' => 'show',
                    'model' => null],
                'manager.manager.download' => [
                    'controller' => 'ManagerController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.userRoles' => [
                'manager.user.index' => [
                    'controller' => 'UserController',
                    'action' => 'index',
                    'model' => null],
                'manager.user.create' => [
                    'controller' => 'UserController',
                    'action' => 'create',
                    'model' => null],
                'manager.user.edit' => [
                    'controller' => 'UserController',
                    'action' => 'edit',
                    'model' => null],
                'manager.user.destroy' => [
                    'controller' => 'UserController',
                    'action' => 'destroy',
                    'model' => null],
                'manager.user.show' => [
                    'controller' => 'UserController',
                    'action' => 'show',
                    'model' => null],
                'manager.user.download' => [
                    'controller' => 'UserController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.userRoleGroupRoles' => [
                'manager.user_role_group.index' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'index',
                    'model' => null
                ],
                'manager.user_role_group.create' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'create',
                    'model' => null
                ],
                'manager.user_role_group.edit' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'edit',
                    'model' => null
                ],
                'manager.user_role_group.destroy' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'destroy',
                    'model' => null
                ],
                'manager.user_role_group.show' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'show',
                    'model' => null
                ],
                'manager.user_role_group.download' => [
                    'controller' => 'UserRoleGroupController',
                    'action' => 'download',
                    'model' => null
                ],
            ],
            'roles.archiveRoles' => [
                'manager.archive.index' => [
                    'controller' => 'ArchiveController',
                    'action' => 'index',
                    'model' => null],
                'manager.archive.destroy' => [
                    'controller' => 'ArchiveController',
                    'action' => 'destroy',
                    'model' => null],
                'manager.archive.download' => [
                    'controller' => 'ArchiveController',
                    'action' => 'download',
                    'model' => null],
            ],
        ];
        return $roles;
    }

    public static function getAdminRoleList()
    {
        $roles = [
            'roles.adminRoleGroupRoles' => [
                'admin.admin_role_group.index' => [
                    'controller' => 'AdminRoleGroupController',
                    'action' => 'index',
                    'model' => null
                ],
                'admin.admin_role_group.create' => [
                    'controller' => 'AdminRoleGroupController',
                    'action' => 'create',
                    'model' => null
                ],
                'admin.admin_role_group.edit' => [
                    'controller' => 'AdminRoleGroupController',
                    'action' => 'edit',
                    'model' => null
                ],
                'admin.admin_role_group.destroy' => [
                    'controller' => 'AdminRoleGroupController',
                    'action' => 'destroy',
                    'model' => null
                ],
                'admin.admin_role_group.show' => [
                    'controller' => 'AdminRoleGroupController',
                    'action' => 'show',
                    'model' => null
                ],
                'admin.admin_role_group.download' => [
                    'controller' => 'AdminRoleGroupController',
                    'action' => 'download',
                    'model' => null
                ],
            ],
            'roles.companyRoles' => [
                'admin.company.index' => [
                    'controller' => 'CompanyController',
                    'action' => 'index',
                    'model' => null
                ],
                'admin.company.create' => [
                    'controller' => 'CompanyController',
                    'action' => 'create',
                    'model' => null
                ],
                'admin.company.edit' => [
                    'controller' => 'CompanyController',
                    'action' => 'edit',
                    'model' => null
                ],
                'admin.company.destroy' => [
                    'controller' => 'CompanyController',
                    'action' => 'destroy',
                    'model' => null
                ],
                'admin.company.show' => [
                    'controller' => 'CompanyController',
                    'action' => 'show',
                    'model' => null
                ],
                'admin.company.download' => [
                    'controller' => 'CompanyController',
                    'action' => 'download',
                    'model' => null
                ],
                'admin.company.login' => [
                    'controller' => 'CompanyController',
                    'action' => 'login',
                    'model' => null
                ],
            ],
            'roles.userRoles' => [
                'admin.user.index' => [
                    'controller' => 'UserController',
                    'action' => 'index',
                    'model' => null
                ],
                'admin.user.download' => [
                    'controller' => 'UserController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.managerRoles' => [
                'admin.manager.index' => [
                    'controller' => 'ManagerController',
                    'action' => 'index',
                    'model' => null
                ],
                'admin.manager.download' => [
                    'controller' => 'ManagerController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.adminRoles' => [
                'admin.admin.index' => [
                    'controller' => 'AdminController',
                    'action' => 'index',
                    'model' => null],
                'admin.admin.create' => [
                    'controller' => 'AdminController',
                    'action' => 'create',
                    'model' => null],
                'admin.admin.edit' => [
                    'controller' => 'AdminController',
                    'action' => 'edit',
                    'model' => null],
                'admin.admin.destroy' => [
                    'controller' => 'AdminController',
                    'action' => 'destroy',
                    'model' => null],
                'admin.admin.show' => [
                    'controller' => 'AdminController',
                    'action' => 'show',
                    'model' => null],
                'admin.admin.download' => [
                    'controller' => 'AdminController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.roleRoles' => [
                'admin.role.index' => [
                    'controller' => 'RoleController',
                    'action' => 'index',
                    'model' => null],
                'admin.role.sync' => [
                    'controller' => 'RoleController',
                    'action' => 'sync',
                    'model' => null],
            ],
            'roles.auditRoles' => [
                'admin.audit.index' => [
                    'controller' => 'AuditController',
                    'action' => 'index',
                    'model' => null],
                'admin.audit.download' => [
                    'controller' => 'AuditController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.archiveRoles' => [
                'admin.archive.index' => [
                    'controller' => 'ArchiveController',
                    'action' => 'index',
                    'model' => null],
                'admin.archive.destroy' => [
                    'controller' => 'ArchiveController',
                    'action' => 'destroy',
                    'model' => null],
                'admin.archive.download' => [
                    'controller' => 'ArchiveController',
                    'action' => 'download',
                    'model' => null],
            ],
            'roles.packageRoles' => [
                'admin.package.index' => [
                    'controller' => 'PackageController',
                    'action' => 'index',
                    'model' => null],
                'admin.package.create' => [
                    'controller' => 'PackageController',
                    'action' => 'create',
                    'model' => null],
                'admin.package.edit' => [
                    'controller' => 'PackageController',
                    'action' => 'edit',
                    'model' => null],
                'admin.package.destroy' => [
                    'controller' => 'PackageController',
                    'action' => 'destroy',
                    'model' => null],
                'admin.package.show' => [
                    'controller' => 'PackageController',
                    'action' => 'show',
                    'model' => null],
                'admin.package.download' => [
                    'controller' => 'PackageController',
                    'action' => 'download',
                    'model' => null],
            ],
        ];
        return $roles;
    }
}
