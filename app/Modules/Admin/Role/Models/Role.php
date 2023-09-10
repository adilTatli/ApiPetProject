<?php

namespace App\Modules\Admin\Role\Models;

use App\Modules\Admin\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'alias',
        'title',
    ];

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return BelongsToMany
     */
    public function perms()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function savePermissions($perms)
    {
        if (!empty($perms)) {
            $this->perms()->sync($perms);
        } else {
            $this->perms()->detach();
        }
    }

    public function hasPermission($alias, $require = false)
    {
        if (is_array($alias)) {
            foreach ($alias as $permissionAlias) {
                $hasPermissions = $this->hasPermission($permissionAlias);
                if ($hasPermissions && !$require) {
                    return true;
                } elseif (!$hasPermissions && $require) {
                    return false;
                }
            }
        } else {
            foreach ($this->perms as $permission) {
                if ($permission->alias == $alias) {
                    return true;
                }
            }
        }

        return $require;
    }
}
