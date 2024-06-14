# Controllers in EasyAdminUltimate

EasyAdminUltimate provides a structured approach to managing different types of controllers within the application. The system is designed with various abstract base controllers and interfaces to streamline configuration and ensure consistent behavior across different panels (admin, user, security, etc.). Below is an overview of these controllers and their roles:

### Table of Contents

- [Base Controllers](#base-controllers)
- [Admin Controllers](#admin-controllers)
- [User Controllers](#user-controllers)
- [Security Controllers](#security-controllers)
- [Marker Interfaces](#marker-interfaces)
- [Example Implementations](#example-implementations)


## Base Controllers

#### AbstractInitialDashboardController

This abstract controller extends `AbstractDashboardController` and contains configurations that are common to all dashboard controllers in EasyAdminUltimate. Any configuration that should apply to all dashboards is defined here.

```php
namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

abstract class AbstractInitialDashboardController extends AbstractDashboardController
{
    // Common configurations for all dashboards
}
```

#### AbstractInitialCrudController

This abstract controller extends `AbstractCrudController` and contains configurations that are common to all CRUD controllers in EasyAdminUltimate.

```php
namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class AbstractInitialCrudController extends AbstractCrudController
{
    // Common configurations for all CRUD controllers
}
```

## Admin Controllers

#### AbstractAdminCrudController

This controller extends `AbstractInitialCrudController` and includes configurations specific to the admin panel. It also implements `AdminControllerInterface`. 

```php
namespace App\Controller\Admin;

use App\Controller\Initial\Abstracts\AbstractInitialCrudController;
use App\Controller\Admin\Interfaces\AdminControllerInterface;

abstract class AbstractAdminCrudController extends AbstractInitialCrudController implements AdminControllerInterface
{
    // Admin-specific configurations for CRUD controllers
}
```

#### AbstractAdminDashboardController

This controller extends `AbstractInitialDashboardController` and includes configurations specific to the admin dashboard, such as the sidebar menu. It also implements `AdminControllerInterface`.

```php
namespace App\Controller\Admin;

use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Controller\Admin\Interfaces\AdminControllerInterface;

abstract class AbstractAdminDashboardController extends AbstractInitialDashboardController implements AdminControllerInterface
{
    // Admin-specific configurations for dashboard controllers
}
```

## User Controllers

#### AbstractUserCrudController

This controller extends `AbstractInitialCrudController` and includes configurations specific to the user panel. It also implements `UserControllerInterface`.

```php
namespace App\Controller\User;

use App\Controller\Initial\Abstracts\AbstractInitialCrudController;
use App\Controller\User\Interfaces\UserControllerInterface;

abstract class AbstractUserCrudController extends AbstractInitialCrudController implements UserControllerInterface
{
    // User-specific configurations for CRUD controllers
}
```

#### AbstractUserDashboardController

This controller extends `AbstractInitialDashboardController` and includes configurations specific to the user dashboard. It also implements `UserControllerInterface`.

```php
namespace App\Controller\User;

use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Controller\User\Interfaces\UserControllerInterface;

abstract class AbstractUserDashboardController extends AbstractInitialDashboardController implements UserControllerInterface
{
    // User-specific configurations for dashboard controllers
}
```

## Security Controllers

#### AbstractSecurityController

This controller extends `AbstractInitialDashboardController` and implements `SecurityControllerInterface`. It serves as the base controller for security-related actions such as login, registration, password reset, etc.

```php
namespace App\Controller\Security;

use App\Controller\Initial\Abstracts\AbstractInitialDashboardController;
use App\Controller\Security\Interfaces\SecurityControllerInterface;

abstract class AbstractSecurityController extends AbstractInitialDashboardController implements SecurityControllerInterface
{
    // Security-specific configurations and methods
}
```

## Marker Interfaces

#### AdminControllerInterface

This interface marks a controller as part of the admin panel.

```php
namespace App\Controller\Admin\Interfaces;

interface AdminControllerInterface
{
    // Marker interface for admin controllers
}
```

#### UserControllerInterface

This interface marks a controller as part of the user panel.

```php
namespace App\Controller\User\Interfaces;

interface UserControllerInterface
{
    // Marker interface for user controllers
}
```

#### SecurityControllerInterface

This interface marks a controller as part of the security panel.

```php
namespace App\Controller\Security\Interfaces;

interface SecurityControllerInterface
{
    // Marker interface for security controllers
}
```

---

## Example Implementations

#### Admin CRUD Controller Example

```php
namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Abstracts\AbstractAdminCrudController;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class UserCrudController extends AbstractAdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('email');
    }
}
```

#### User Dashboard Controller Example

```php
namespace App\Controller\User;

use App\Controller\User\Abstracts\AbstractUserDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractUserDashboardController
{
    #[Route("/user/dashboard")]
    public function index(): Response
    {
        return $this->render('user/dashboard.html.twig');
    }
}
```

#### Security Controller Example

```php
namespace App\Controller\Security;

use App\Controller\Security\Abstracts\AbstractSecurityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractSecurityController
{
    #[Route("/login", name="app_login")]
    public function login(Request $request): Response
    {
        // Handle login logic
        return $this->render('security/login.html.twig');
    }
}
```

### Summary

By utilizing these abstract controllers and interfaces, EasyAdminUltimate ensures a clear separation of concerns and consistent configurations across different parts of the application. This structure helps maintain a clean and scalable codebase, making it easier to manage and extend. For detailed implementation and advanced usage, please refer to the internal source code and documentation of EasyAdminUltimate.

[Back To Documentation Homepage](../index.md)