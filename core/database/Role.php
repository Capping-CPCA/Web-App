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
 * @version 0.3.2
 * @since 0.1
 */
class Role {
    const NewUser     = 0b00001;
    const User        = 0b00011;
    const Coordinator = 0b00111;
    const Admin       = 0b01111;
    const Superuser   = 0b11111;

    /**
     * Maps database permissionlevel to PHP Role
     * @param $permissionLevel string the permissionlevel from the database
     * @return int the role number assigned to the user
     */
    static function roleFromPermissionLevel($permissionLevel) {
        switch ($permissionLevel) {
            case 'User':
            case 'Facilitator':
                return Role::User;
            case 'Coordinator':
                return Role::Coordinator;
            case 'Administrator':
                return Role::Admin;
            case 'Superuser':
                return Role::Superuser;
            case 'New':
            default:
                return Role::NewUser;
        }
    }
}