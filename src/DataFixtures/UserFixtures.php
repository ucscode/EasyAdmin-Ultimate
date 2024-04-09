<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserMeta;
use App\Immutable\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    const FIXTURES = [
        1 => [
            'uuid' => '87e60f2b-09c3-4fec-8312-8c21fa267fd3',
            'roles' => [
                UserRole::ROLE_ADMIN,
            ],
            'password' => '12345',
            'email' => 'root@localhost.com',
            'username' => 'ucscode',
            'meta' => [
                'balance' => '2000'
            ]
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach(self::FIXTURES as $data) {

            $user = new User();

            $user->setUuid($data['uuid']);
            $user->setRoles($data['roles']);
            $user->setPassword($data['password']);
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);

            foreach($data['meta'] as $key => $value) {
                $meta = new UserMeta($key, $value);
                $user->addMeta($meta);
            }
            
            $manager->persist($user);
        }

        $manager->flush();
    }
}
