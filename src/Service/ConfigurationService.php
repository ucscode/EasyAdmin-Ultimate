<?php

namespace App\Service;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Yaml\Yaml;

class ConfigurationService
{
    protected array $configuration;
    protected PropertyAccessor $propertyAccessor;

    public function __construct(protected KernelInterface $kernel, protected ParameterBagInterface $parameterBag)
    {
        $configurationPath = $this->kernel->getProjectDir() . '/config/eau.yaml';
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->configuration = Yaml::parseFile($configurationPath);
        $this->preprocessConfiguration();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->accessPropertyValue($key);

        if(is_string($value)) {
            $value = $this->preprocessor(trim($value), [
                'self\\(%s\\)' => fn (string $key) => $this->get($key)
            ]);

            if($value === '' && $default !== null) {
                $value = $default;
            }
        }

        return $value ?? $default;
    }

    protected function accessPropertyValue(string $key): mixed
    {
        $keys = array_map(fn ($offset) => sprintf("[%s]", trim($offset)), explode(".", $key));
        return $this->propertyAccessor->getValue($this->configuration, implode($keys));
    }

    /**
     * Processes a string by replacing specified patterns with their corresponding values.
     *
     * This function takes a string and an associative array of regex patterns and callbacks.
     * Each pattern includes a single "%s" placeholder, which captures a key to be processed by the corresponding callback function.
     * The function returns the processed string with all matches replaced by their respective callback return values.
     *
     * @param string $value The string containing patterns to be replaced.
     * @param array $qualifiers Associative array mapping regex patterns to callbacks.
     *
     * @return mixed The processed string with patterns replaced, or the original value if no replacements are made.
     */
    protected function preprocessor(string $value, ?array $qualifiers = null): string
    {
        foreach($qualifiers as $regex => $callback) {
            $value = preg_replace_callback(sprintf('~(?:%%%s%%)+?~', sprintf($regex, '([\\w\\.]+)')), function ($matches) use ($callback) {

                if(!is_callable($callback)) {
                    throw new InvalidArgumentException('Configuration preprocessor value must be of type callable');
                }

                $result = call_user_func($callback, $matches[1] ?? '');

                return is_bool($result) ? (int)$result : $result;
            }, $value);
        }

        return $value;
    }

    private function preprocessConfiguration(): void
    {
        array_walk_recursive($this->configuration, function (&$value) {
            if(is_string($value)) {
                $value = $this->preprocessor(trim($value), [
                    'env\\(%s\\)' => fn (string $key) => $_ENV[$key] ?? '',
                    '%s' => fn (string $key) => $this->parameterBag->get($key),
                ]);
            }
        });
    }
}
