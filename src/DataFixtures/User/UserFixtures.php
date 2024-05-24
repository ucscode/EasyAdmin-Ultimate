<?php

namespace App\DataFixtures\User;

use App\Entity\User\User;
use App\Utils\RoleUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * This fixture has not been resolved properly and may generate error
 */
class UserFixtures extends Fixture
{
    public const LIMIT = 5;

    public const DEFAULT_USER = [
        'uniqueId' => '87e60f2b',
        'roles' => [
            RoleUtils::ROLE_ADMIN,
        ],
        'password' => '123456',
        'email' => 'root@localhost.com',
        'username' => 'admin',
        'isVerified' => true,
    ];

    public function __construct(protected UserPasswordHasherInterface $passwordHasher)
    {
        //
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $data = self::DEFAULT_USER;

        for($x = 0; $x < self::LIMIT; $x++) {
            if($x) {
                $data = [
                    'uniqueId' => $faker->regexify('\w{7}'), // (new KeyGenerator())->generateKey(8),
                    'roles' => [
                        RoleUtils::ROLE_USER
                    ],
                    'password' => '123456',
                    'email' => $faker->email(),
                    'username' => $faker->userName(),
                    'isVerified' => $faker->boolean(),
                ];
            }

            $user = (new User())
                ->setUniqueId($data['uniqueId'])
                ->setRoles($data['roles'])
                ->setEmail($data['email'])
                ->setUsername($data['username'])
                ->setIsVerified($data['isVerified'])
            ;

            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
