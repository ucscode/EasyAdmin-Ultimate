<?php

namespace App\Event\Listener\User;

use App\Configuration\UserPropertyPattern;
use App\Constants\ModeConstants;
use App\Entity\User\Property;
use App\Entity\User\User;
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
    /**
     * do not add __constructor() with dependency injection unless 
     * it has been properly configured in the service.yaml file
     * Otherwise, this listener will be ignored 
     */

    public function prePersist(User $user, PrePersistEventArgs $args): void
    {
        $pattern = new UserPropertyPattern();

        /**
         * @var \Symfony\Component\HttpFoundation\ParameterBag $parameterBag
         */
        foreach($pattern->getPatterns() as $name => $parameterBag) {
            $property = new Property($name, $parameterBag->get('value'), $parameterBag->get('mode'));
            $user->addProperty($property);
        }
    }
}