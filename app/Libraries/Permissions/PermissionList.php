<?php

namespace App\Libraries\Permissions;
class PermissionList
{
    public static function getUserRoleList()
    {

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
        ];
        return $roles;
    }
}
