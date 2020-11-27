<?php

namespace SomeoneFamous\PermissionTree\Models;

use Illuminate\Database\Eloquent\Model;
use SomeoneFamous\FindBy\Traits\FindBy;

class Permission extends Model
{
    use FindBy;

    protected $fillable = [
        'parent_id',
        'code',
        'name',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function hasSameStatusAsParent($user): bool
    {
        return (($parent = $this->parent) && ($user->hasPermission($this) == $user->hasPermission($parent)));
    }

    public function detachAllChildren($subject)
    {
        if ($this->children()->count() > 0) {

            foreach ($this->children as $child) {

                $subject->permissions()->detach($child->id);
                $child->detachAllChildren($subject);
            }
        }
    }

    public static function createManyByArray(array $permissions, $parent_id = null)
    {
        foreach ($permissions as $permission_code => $permission_info) {

            $permission = self::create([
                'parent_id' => $parent_id,
                'code' => $permission_code,
                'name' => is_array($permission_info) ? $permission_info['name'] : $permission_info
            ]);

            if (is_array($permission_info)) {
                self::createManyByArray($permission_info['children'], $permission->id);
            }
        }
    }
}
