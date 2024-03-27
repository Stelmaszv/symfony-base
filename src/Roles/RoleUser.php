<?php


namespace App\Roles;

use App\Security\Atribute;

class RoleUser
{
    public const NAME = 'ROLE_USER';

	public const ROLES = [
		Atribute::SHOW_CAR
	];
}