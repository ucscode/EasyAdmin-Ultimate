# JsonRequest

The `JsonRequest` class in EasyAdminUltimate extends Symfony's `ParameterBag` and provides a convenient way to fetch request data sent in `application/json` format. This class automates the process of retrieving JSON data from the request body, which is not natively handled by Symfony when dealing with raw JSON data.

## Features

### 1. Automatic JSON Data Extraction
JsonRequest simplifies the retrieval of JSON data from incoming requests, abstracting away the manual handling required when Symfony's request handling does not natively parse raw JSON.

### 2. Integration with ParameterBag
By extending Symfony's `ParameterBag`, JsonRequest inherits functionalities for managing and accessing request data within the application.

## Example Usage

Below is an example demonstrating how to use JsonRequest to fetch JSON data from a request:

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\JsonRequest;

class MyController extends AbstractController
{
    protected JsonRequest $jsonRequest;

    public function __construct(JsonRequest $jsonRequest)
    {
        $this->jsonRequest = $jsonRequest;
    }

    public function yourMethod()
    {
        $name = $this->jsonRequest->get('name');
        // $name will contain the value of 'name' from the JSON request body
    }
}
```

In this example:
- The `JsonRequest` service is injected into the controller through the constructor.
- The `get` method of `JsonRequest` is used to retrieve the value associated with `'name'` from the JSON request body.

### Handling JSON Data in Requests

When the request body contains JSON data, JsonRequest automatically parses it for easy access:

```json
{
    "name": "John Doe",
    "age": 30,
    "city": "New York"
}
```

You can retrieve values from this JSON data using JsonRequest:

```php
$name = $this->jsonRequest->get('name'); // John Doe
$age = $this->jsonRequest->get('age'); // 30
$city = $this->jsonRequest->get('city'); // New York
```

---

This concludes the overview of `JsonRequest`. For more detailed information and advanced usage, please refer to the internal source code of EasyAdminUltimate.