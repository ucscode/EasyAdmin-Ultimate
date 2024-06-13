# AffiliationService

The `AffiliationService` in EasyAdminUltimate is designed to manage downlines and hierarchical structures using the adjacency list model to track referrals. This service provides various methods to handle user relationships, such as retrieving children or ancestors of a user, generating user referral links, and verifying parent-child relationships at any level.

## Features

### 1. Manage Downlines and Hierarchies
The `AffiliationService` facilitates the management of downlines and hierarchical structures, enabling effective tracking and organization of user relationships.

### 2. Adjacency List Model
EasyAdminUltimate uses the adjacency list model to maintain and manage the referral structure. This model is efficient for managing hierarchical data and is suitable for applications that require such relationships.

### 3. Retrieve Children or Ancestors
With this service, you can easily fetch all the children or ancestors of a particular user, allowing for detailed examination and management of user hierarchies.

### 4. Generate Referral Links
The `AffiliationService` provides methods to generate referral links for users, streamlining the process of affiliate marketing and user invitations.

### 5. Verify Parent-Child Relationships
You can use this service to check if a user has a specific child or parent at any level within the hierarchy, ensuring accurate and reliable relationship management.

## Example Usage

Below is an example demonstrating how to include and utilize the `AffiliationService` within a method:

```php
namespace App\Controller;

use App\Service\AffiliationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyController extends AbstractController
{
    protected AffiliationService $affiliationService;

    public function __construct(AffiliationService $affiliationService)
    {
        $this->affiliationService = $affiliationService;
    }

    public function yourMethod()
    {
        $childEntity = ...; // Retrieve or define the child entity
        $hasChild = $this->affiliationService->hasChild($this->getUser(), $childEntity);
        // $hasChild will be true or false
    }
}
```

This example shows how to inject the `AffiliationService` into a class and use the `hasChild` method to check for a parent-child relationship.

---

This concludes the overview of the `AffiliationService`. For more detailed information and advanced usage, please refer to the intenral code.