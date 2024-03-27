<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Roles\RoleUser;
use App\Roles\RoleSuperAdmin;
use App\Generic\Components\AbstractFixtureGeneric;

class UserFixtures extends AbstractFixtureGeneric
{
    protected ?string $enetity = User::class;
    protected array $data = [
        [
          'email' => 'user@qwe.com',
          'roles' => [RoleSuperAdmin::NAME],
          'password' => '123'
        ],
        [
            'email' => 'kot123@dot.com',
            'roles' => [RoleUser::NAME],
            'password' => 'qwe'
        ],
        [
            'email' => 'pani@wp.pl',
            'roles' => [RoleUser::NAME],
            'password' => 'vbn'
        ]
    ];

    public function onPasswordSet(mixed $value,object $entity){
        return $this->passwordEncoder->hashPassword(
            $entity,
            $value
        );
    }
}