<?php

namespace App\Event\Listener\User;

use App\Entity\User\Property;
use App\Entity\User\User;
use App\Enum\ModeEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

/**
 * Doctrine provides a lightweight event system to update entities during the application execution.
 * Doctrine triggers events `before`/`after` performing the most common entity operations (e.g. prePersist/postPersist, preUpdate/postUpdate) and also on other common tasks.
 * 
 * @see https://symfony.com/doc/7.1/doctrine/events.html
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/events.html#entity-listeners
 */

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
class UserPrePersistListener
{
    public function prePersist(User $user, PrePersistEventArgs $args): void
    {
        // UserPropertyConfig::getConfigurations();
        $this->setDefaultProperties($user);
    }

    /**
     * The properties defined here are simply examples
     * You should define properties that suits your project
     */
    private function setDefaultProperties(User $user): void
    {
        $readWritePermission = ModeEnum::READ->value|ModeEnum::WRITE->value;

        // admin have permission to read and write
        $user->addProperty(new Property('firstName', null, $readWritePermission));
        $user->addProperty(new Property('lastName', null, $readWritePermission));
        $user->addProperty(new Property('about', 'Tell us about you', $readWritePermission));

        // readonly permission (no write access granted)
        $user->addProperty(new Property('balance', 0.00, ModeEnum::READ, 'Your Revenue'));

        // admin does not have permission to access the property
        $user->addProperty(new Property('hasPaid', false, ModeEnum::NO_PERMISSION));
    }
}