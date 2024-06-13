# JsPayload

The `JsPayload` class in EasyAdminUltimate extends `ParameterBag` and facilitates passing data directly from PHP to JavaScript without needing explicit exports. This feature simplifies communication between server-side and client-side environments by allowing easy access to PHP-defined variables in JavaScript modules through a unified context.

## Features

### 1. Seamless Data Transfer
JsPayload enables seamless transfer of data from PHP backend to JavaScript frontend, ensuring that all data set in the payload is directly accessible in the JavaScript context.

### 2. Integration with ParameterBag
By extending Symfony's `ParameterBag`, JsPayload inherits functionalities for managing and accessing data within the application, enhancing flexibility and ease of use.

## Example Usage

Below is an example demonstrating how to use JsPayload to pass and access data between PHP and JavaScript:

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JsPayload;

class MyController extends AbstractController
{
    protected JsPayload $jsPayload;

    public function __construct(JsPayload $jsPayload)
    {
        $this->jsPayload = $jsPayload;
    }

    public function yourMethod()
    {
        $this->jsPayload->add([
            'name' => 'John',
            'age' => 20,
        ]);

        $this->jsPayload->set('meal', ['key' => 'yes']);

        // No need to pass $this->jsPayload to render method

        return $this->render('your_template.html.twig');
    }
}
```

In this example:
- The `JsPayload` service is injected into the controller through the constructor.
- The `add` method is used to add multiple variables (`'name'` and `'age'`).
- The `set` method is used to set a specific variable `'meal'` with an array value `['key' => 'yes']`.

### Accessing in JavaScript

You can access the data directly in JavaScript by importing `appService` module:

```javascript
import { appService } from '/resource/js/app-service.js';

const payload = appService.getPayload();

console.log(payload.name); // John
console.log(payload.age); // 20
console.log(payload.meal.key); // yes
```

In this example, `appService.getPayload()` retrieves the entire payload set in PHP, allowing direct access to all variables like `'name'`, `'age'`, and `'meal'` with their respective values.

---

This concludes the overview of `JsPayload`. For more detailed information and advanced usage, please refer to the internal source code of EasyAdminUltimate.