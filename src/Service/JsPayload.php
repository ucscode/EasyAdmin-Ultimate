<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\ParameterBag;

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
class JsPayload extends ParameterBag
{}