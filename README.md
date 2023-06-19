## PHP client for Security service

### Installation

### Usage

```php
use SecurityServiceClient\SecurityServiceClient;

$securityServiceClient = new SecurityServiceClient('api-key');

// Log user login
$suspicionScore = $securityServiceClient->logUserLogin(42, '1.1.1.1', 'Mozilla 123');

// Fetch logins of users 1 and 2 (you can only fetch users of your company)

$logins = $securityServiceClient->fetchUsersLogins([1, 2]);
```
