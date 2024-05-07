<?php

namespace App\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Yaml;

class ConfigurationService
{
    protected array $configuration;
    protected PropertyAccessor $propertyAccessor;

    public function __construct(KernelInterface $kernel)
    {
        $configurationPath = $kernel->getProjectDir() . '/config/uss.ea.yaml';
        $this->configuration = Yaml::parseFile($configurationPath);
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function get(string $key): mixed
    {
        $keys = array_map(fn ($offset) => sprintf("[%s]", trim($offset)), explode(".", $key));
        return $this->propertyAccessor->getValue($this->configuration, implode($keys));
    }
}
