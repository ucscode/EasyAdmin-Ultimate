<?php

namespace App\DataFixtures\User;

use App\Entity\User\Notification;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class NotificationFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /** @var \App\Repository\User\UserRepository */
        $userRepository = $manager->getRepository(User::class);

        $user = $userRepository->findOneBy(['uniqueId' => UserFixtures::DEFAULT_USER['uniqueId']]);

        $notification = $this->notificationFactory($user, Factory::create());
    }

    protected function notificationFactory(User $user, Generator $faker): Notification
    {
        $notification = new Notification();

        $notification->setMessage($faker->text());
        $notification->setUser($user);

        return $notification;
    }
}