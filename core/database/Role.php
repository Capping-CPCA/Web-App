<?php
/**
 * PEP Capping 2017 Algozzine's Class
 *
 * Role class for managing permissions.
 *
 * This class gives the basic roles that are defined in the
 * system. The roles can be mapped to the permissionlevels in the
 * database using the #roleFromPermissionLevel() function. The roles
 * use powers of 2 to make bitwise operations simpler.
 *
 * @author Jack Grzechowiak
 * @copyright 2017 Marist College
 * @version 0.1.5
 * @since 0.1
 */
class Role {
    const User = 1;
    const Facilitator = 2;
    const Coordinator = 4;
    const Admin = 8;
    const SuperAdmin = 16;

    /**
     * Maps database permissionlevel to PHP Role
     * @param $permissionLevel string the permissionlevel from the database
     * @return int the role number assigned to the user
     */
    static function roleFromPermissionLevel($permissionLevel) {
        switch ($permissionLevel) {
            case 'Facilitator':
                return Role::Facilitator;
            case 'Coordinator':
                return Role::Coordinator;
            case 'Administrator':
                return Role::Admin;
            case 'Superuser':
                return Role::SuperAdmin;
            case 'User':
            default:
                return Role::User;
        }
    }
}