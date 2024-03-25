<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Generic\Fixture\FixtureGeneric;

class UserFixtures extends FixtureGeneric
{
    protected ?string $enetity = User::class;
    protected array $data = [
        [
          'email' => 'user@qwe.com',
          'roles' => ['ROLE_SUPER_ADMIN'],
          'password' => '123'
        ],
        [
            'email' => 'kot123@dot.com',
            'roles' => ['ROLE_UESER'],
            'password' => 'qwe'
        ],
        [
            'email' => 'pani@wp.pl',
            'roles' => ['ROLE_UESER'],
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