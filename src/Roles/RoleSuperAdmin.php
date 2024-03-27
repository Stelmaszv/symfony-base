<?php


namespace App\Roles;

use App\Security\Atribute;

class RoleSuperAdmin
{
	public const NAME = 'ROLE_SUPER_ADMIN';

	public const ROLES = [
		Atribute::REMOVE_CAR,
		Atribute::ADD_CAR,
		Atribute::UPDATE_CAR,
		Atribute::SHOW_CAR,
	];
}