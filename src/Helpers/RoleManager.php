<?php

namespace DaniloPolani\FusionAuthJwt\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class RoleManager
{
    /**
     * Check if current authenticated user has a role or is a super admin.
     *
     * @param  string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        return (self::isSuperAdmin() || in_array($role, self::getRoles()));
    }

    /**
     * Check if current authenticated user has a super admin role.
     *
     * @param  string $role
     * @return bool
     */
    public static function isSuperAdmin(): bool
    {
        return in_array(Config::get('fusionauth.super_role'), self::getRoles());
    }

    /**
     * Get all roles of authenticated user.
     * If guest, an empty array will be returned.
     *
     * @return array
     */
    public static function getRoles(): array
    {
        // @phpstan-ignore-next-line
        return optional(Auth::guard('fusionauth')->user())->roles ?: [];
    }
}
