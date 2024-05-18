<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use App\Entity\User\Property;
use App\Utils\Stateless\RoleUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const FIXTURES = [
        1 => [
            'uniqueId' => '87e60f2b',
            'roles' => [
                RoleUtils::ROLE_ADMIN,
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

            $user->setUniqueId($data['uniqueId']);
            $user->setRoles($data['roles']);
            $user->setPassword($data['password']);
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);

            foreach($data['meta'] as $key => $value) {
                $meta = new Property($key, $value);
                $user->addUserProperty($meta);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
