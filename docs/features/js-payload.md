# JsPayload

The `JsPayload` class in EasyAdminUltimate is a servce that extends `ParameterBag` and facilitates passing data directly from PHP to JavaScript without needing explicit exports. This feature simplifies communication between server-side and client-side environments by allowing easy access to PHP-defined variables in JavaScript modules through a unified context.

## Example Usage

Below is an example demonstrating how to use JsPayload to pass and access data between PHP and JavaScript:

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JsPayload;

class MyController extends AbstractController
{
    public function __construct(protected JsPayload $jsPayload)
    {
        // Using symfony autowire mechanism
    }

    public function customMethod()
    {
        // Add multiple data
        $this->jsPayload->add([
            'name' => 'John',
            'age' => 20,
        ]);

        // set a single data
        $this->jsPayload->set('meal', ['key' => 'yes']);

        /**
         * No need to pass $this->jsPayload to the render method
         * It is handled internally by Eau Twig Extension
         */

        return $this->render('your_template.html.twig');
    }
}
```

In this example:
- The `JsPayload` service is injected into the controller through the constructor.
- The `add` method is used to add multiple variables (`'name'` and `'age'`).
- The `set` method is used to set a specific variable `'meal'` with an array value `['key' => 'yes']`.

## Accessing in JavaScript

You can access the data directly in JavaScript by importing `service` module:

```javascript
import { service } from '/resource/js/service.js';

const payload = service.getPayload();

console.log(payload.name); // John
console.log(payload.age); // 20
console.log(payload.meal.key); // yes
```

In this example, `appService.getPayload()` retrieves the entire payload set in PHP, allowing direct access to all variables like `'name'`, `'age'`, and `'meal'` with their respective values.

## Please Note:

If your template does not directly (or indirectly) extend `bundles/EasyAdminBundle/layout.html.twig`, then you must add the following line of code to your template:

```twig
{% include 'bundles/EasyAdminBundle/section/js_payload.html.twig' %}
```

---

This concludes the overview of `JsPayload`. For more detailed information and advanced usage, please refer to the internal source code of EasyAdminUltimate.

[Back To Documentation Homepage](../index.md)