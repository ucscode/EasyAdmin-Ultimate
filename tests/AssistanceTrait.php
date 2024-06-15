<?php

namespace App\Tests;

trait AssistanceTrait
{
    protected function updateInaccessibleProperty(object $instance, string $property, mixed $value): void
    {
        $reflection = new \ReflectionClass($instance);
        $reflectedProperty = $reflection->getProperty($property);
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($instance, $value);
    }
}