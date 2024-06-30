<?php

namespace App\Service;

/**
 * Class JsPayload
 *
 * This class extends Symfony's ParameterBag and serves as a bridge for transferring data from PHP to JavaScript.
 * It provides a structured way to store data in PHP that will be automatically available in JavaScript.
 *
 * The data stored in an instance of this class can be accessed in JavaScript by importing and using the `appService` from "/resource/js/app-service.js".
 * For example, to get all the context stored in the ParameterBag, you can use `appService.getContext()`.
 *
 * ## Usage (Service):
 *
 * ```php
 * $this->jsPayload->set("name", "John Doe")
 * ```
 *
 * To use this service, import and utilize the appService in your JavaScript code.
 *
 * ```javascript
 * import { appService } from "/resource/js/app-service.js";
 * const context = appService.getContext();
 * console.log(context.name); // John Doe
 * ```
 * @package App\Service
 */
class JsPayload
{
    protected array $payload = [];

    public function __construct(array $parameters = [])
    {
        $this->payload = array_replace($this->payload, $parameters);
    }

    public function set(string $key, mixed $value): static
    {
        $this->payload[$key] = $value;

        return $this;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->payload[$key] ?? $default;
    }

    public function remove(string $key): static
    {
        if(array_key_exists($key, $this->payload)) {
            unset($this->payload[$key]);
        }

        return $this;
    }
    
    public function all(): array
    {
        return $this->payload;
    }
}