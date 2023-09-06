<?php


namespace App\Libraries;


class UserRole
{
    private $roles = [];


    public function getRoles($user)
    {
        if (
            !isset($this->roles[class_basename($user)][$user->id])
            || empty($this->roles[class_basename($user)][$user->id])
        ) {
            $this->fillRoles($user);
        }
        return $this->roles[class_basename($user)][$user->id];
    }

    public function fillRoles($user)
    {
        $result = [];

        $roles = $user->roleGroup->roles->all();

        foreach ($roles as $role) {
            $result[$role->route_name] = $role;
        }
        $this->roles[class_basename($user)][$user->id] = $result;
    }
}
