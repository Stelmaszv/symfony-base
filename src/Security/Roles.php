<?php
namespace App\Security;

class Roles
{
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
    public const ROLE_USER = 'ROLE_USER';
    private const ROLES = [

        self::ROLE_SUPER_ADMIN => [
            Atribute::REMOVE_CAR,
            Atribute::ADD_CAR,
            Atribute::UPDATE_CAR,
            Atribute::SHOW_CAR,
        ],

        self::ROLE_USER => [
            Atribute::SHOW_CAR,
        ]

    ];

    static function checkAtribute(string $role,$atribute){
        return in_array($atribute, self::ROLES[$role]);
    }
}