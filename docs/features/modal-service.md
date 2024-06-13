# ModalService

The `ModalService` in EasyAdminUltimate provides functionality to display modal dialogs in a manner similar to Symfony's flash messages but tailored for more interactive and persistent displays. This service allows you to define and manage modals that can be shown once or persistently across different parts of your application.

## Features

### 1. Display Modals
ModalService facilitates the display of modal dialogs to convey important messages, forms, or user interactions without navigating away from the current page.

### 2. Customizable Content
You can customize modal content, including headers, buttons, and additional features to suit specific application needs.

### 3. Persistent Display
Modals can be configured to persist across page loads until dismissed by the user, providing a persistent way to communicate information or gather user input.

## Example Usage

Below is an example demonstrating how to use ModalService to display modals in your application:

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\Modal\Modal;
use App\Model\Modal\ModalButton;
use App\Service\ModalService;

class MyController extends AbstractController
{
    protected ModalService $modalService;

    public function __construct(ModalService $modalService)
    {
        $this->modalService = $modalService;
    }

    public function yourMethod()
    {
        $modal = (new Modal())
            ->setContent('Show this in modal')
            ->setTitle('An optional header')
            ->setVisible(true); // To render the modal immediately when the page loads
            ->addButton(new ModalButton('Close', 'btn-secondary', 'data-bs-dismiss="modal"'))
            // Add more buttons or customize as needed
            // other configurations
        ;

        $this->modalService->addModal($modal);

        /**
         * Optionally, render a template that includes modal-wrappers.html.twig for display
         * if your template extends the custom Easyadmin layout, the modal-wrappers will be available already
         */
        return $this->render('your_template.html.twig');
    }
}
```

In this example:
- `ModalService` is injected into the controller through the constructor.
- A new `Modal` instance is created and configured with content, title, visibility settings, and optional buttons using `ModalButton`.
- `addModal` method of `ModalService` is used to add the modal for display.

### Integration with Twig Templates

If your template does not inherit the utility directly, you can include the modal wrappers template to render modals:

```twig
{# your_template.html.twig #}

{% block body %}
    {# Render any other content #}

    {% include 'utility/modals/modal-wrappers.html.twig' %}
{% endblock %}
```

In this Twig template:
- `utility/modals/modal-wrappers.html.twig` is included to render modals added via `ModalService`.
- The included template ensures that all modals added through `ModalService` are rendered appropriately.

---

This concludes the overview of `ModalService` in EasyAdminUltimate. For more detailed information and advanced usage, please refer to the internal source code and documentation of EasyAdminUltimate.