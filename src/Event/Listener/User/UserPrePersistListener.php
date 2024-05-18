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
        $this->setDefaultProperties($user);
    }

    /**
     * The properties defined here are simply examples
     * You should define properties that suits your project
     */
    private function setDefaultProperties(User $user): void
    {
        $readAndWritePermission = ModeEnum::READ->value|ModeEnum::WRITE->value;

        // user have permission to read and write
        $user->addProperty(new Property('firstName', null, $readAndWritePermission, 'First Name'));
        $user->addProperty(new Property('lastName', null, $readAndWritePermission, 'Last Name'));
        $user->addProperty(new Property('about', 'Tell us about you', $readAndWritePermission, 'About You'));

        // readonly permission (no write access granted)
        $user->addProperty(new Property('balance', 0.00, ModeEnum::READ, 'Your Revenue'));

        // user does not have permission to access the property
        $user->addProperty(new Property('hasPaid', false, ModeEnum::NO_PERMISSION));
    }
}