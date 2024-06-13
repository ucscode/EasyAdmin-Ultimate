# ConfigurationService

The `ConfigurationService` in EasyAdminUltimate provides access to configuration data stored in the `/project_dir/config/eau.yaml` file. This service allows you to parse and retrieve nested configuration settings using dot notation for convenient access.

## Example Usage

Below is an example demonstrating how to include and use the `ConfigurationService` within a class:

```yaml
# /config/eau.yaml

app:
    name: 'EasyAdmin Ultimate'
```

```php
namespace App\Controller;

use App\Service\ConfigurationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyController extends AbstractController
{
    protected ConfigurationService $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    public function yourMethod()
    {
        $name = $this->configurationService->get('app.name'); // EasyAdmin Ultimate
    }
}
```

In this example, the `ConfigurationService` is injected into the controller through the constructor. The `yourMethod` function demonstrates how to use the `get` method of the `ConfigurationService` to retrieve the value of `app.name` from the `eau.yaml` configuration file.

---

This concludes the overview of the `ConfigurationService`. For more detailed information and advanced usage, please refer to the internal source code of EasyAdminUltimate.