<?php

namespace App\DataFixtures\User;

use App\Entity\User\Notification;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class NotificationFixture extends Fixture implements DependentFixtureInterface
{
    public const LIMIT = 9;

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        /** @var \App\Repository\User\UserRepository */
        $userRepository = $manager->getRepository(User::class);

        $user = $userRepository->findOneBy(['uniqueId' => UserFixtures::DEFAULT_USER['uniqueId']]);

        for($x = 0; $x < self::LIMIT; $x++) {
            $notification = $this->notificationFactory($user, Factory::create());
            $manager->persist($notification);
        }

        $manager->flush();
    }

    protected function notificationFactory(User $user, Generator $faker): Notification
    {
        $notification = new Notification();

        $notification->setMessage($faker->text());
        $notification->setUser($user);
        $notification->setImageUrl($faker->boolean() ? $faker->imageUrl(50, 50, 'people') : null);
        $notification->setActionUrl($faker->boolean() ? $faker->url() : null);
        $notification->setSeenByUser($faker->boolean());

        return $notification;
    }
}