<?php

namespace SomeoneFamous\PermissionTree\Traits;

use Illuminate\Support\Str;
use SomeoneFamous\PermissionTree\Models\Permission;

trait HasPermissions
{
    public $permissions_cache = [];

    public function __call($called_method, $arguments)
    {
        foreach (['can', 'allowTo', 'dontAllowTo'] as $method) {

            if (Str::startsWith($called_method, $method)) {

                return $this->$method(
                    Str::snake(Str::after($called_method, $method)), $arguments
                );
            }
        }

        return parent::__call($called_method, $arguments);
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'permittable')->withTimestamps()->withPivot('allowed');
    }

    public function allowedPermissions()
    {
        return $this->permissions()->wherePivot('allowed', '=', true);
    }

    public function disallowedPermissions()
    {
        return $this->permissions()->wherePivot('allowed', '=', false);
    }

    public function addPermission(Permission $permission)
    {
        if (!$this->hasPermission($permission)) {
            if ($parent = $permission->parent()->first()) {

                if ($permission->hasSameStatusAsParent($this)) {
                    $this->permissions()->detach($permission->id);
                    $this->permissions()->attach($permission->id);
                } else {
                    $this->permissions()->detach($permission->id);
                }

                $permission->detachAllChildren($this);
            } else {
                $this->permissions()->detach();
                $this->permissions()->attach($permission->id);
            }
        }

        return $this;
    }

    public function removePermission(Permission $permission)
    {
        if ($this->hasPermission($permission)) {
            if ($parent = $permission->parent()->first()) {

                $permission->detachAllChildren($this);

                if ($permission->hasSameStatusAsParent($this)) {
                    $this->permissions()->detach($permission->id);
                    $this->permissions()->attach($permission->id, ['allowed' => false]);
                } else {
                    $this->permissions()->detach($permission->id);
                }
            } else {
                $this->permissions()->detach();
            }
        }

        return $this;
    }

    public function allowTo(string $permission_code, $arguments = [])
    {
        if ($permission = Permission::findByCode($permission_code)) {

            $this->addPermission($permission);
        }

        return $this;
    }

    public function dontAllowTo(string $permission_code, $arguments = [])
    {
        if ($permission = Permission::findByCode($permission_code)) {

            $this->removePermission($permission);
        }

        return $this;
    }

    public function hasPermission(Permission $permission): bool
    {
        if (array_key_exists($permission->id, $this->permissions_cache)) {

            return $this->permissions_cache[$permission->id];
        }

        $allowed    = $this->allowedPermissions()->pluck('permission_id')->all();
        $disallowed = $this->disallowedPermissions()->pluck('permission_id')->all();

        while ($permission) {

            if (in_array($permission->id, $disallowed)) {
                return false;
            }

            if (in_array($permission->id, $allowed)) {
                return true;
            }

            $permission = $permission->parent;
        }

        return false;
    }

    public function can($permission_code, $arguments = []): bool
    {
        return ($permission = Permission::findByCode($permission_code))
            ? $this->hasPermission($permission)
            : false;
    }
}
