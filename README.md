# PHP Library Authenticaton #

Authenticaton classes

## Installation ##

Install using composer

```
composer require sinevia/php-library-authentication
```

## Usage ##

### 1. Identity management ###

The Authentication class helps you reliably keep the authenticated users during the current session.

```
\Sinevia\Authentication::getInstance()->setIdentity('user', $loggedinUser->Id); // After successful login
$userId = \Sinevia\Authentication::getInstance()->getIdentity('user'); // During session
\Sinevia\Authentication::getInstance()->reoveIdentity('user'); // On logout
```

### 2. Namespaced Identities ###

Probably the greatest features is the support of namespaces. The namespaces allows to keep separation betweern different areas of responsibility. The following example will help illustrate the namespaces better. A website may be separated into manager area, clients area, employee area, each with different functions. Upon login a manager can be set to authenticated both as a manager and employee (example 1). Upon attempting to access the area his access can be checked before being allowed/denied to continue (example 2).

Example 1. Add the current user both to the manager and employee namespace

```
if ($isManager) {
    \Sinevia\Authentication::getInstance()->setIdentity('manager', $loggedinUser->Id);
}
if ($isEmployee) {
    \Sinevia\Authentication::getInstance()->setIdentity('employee', $loggedinUser->Id);
}
```

Example 2. Check if user is allowed access to the manager area and allow/deny access

```
// Check if the user is part of the manager namespace
$managerId = \Sinevia\Authentication::getInstance()->getIdentity('manager');

// Check if user cannot access the manager access
$cannotAccessManagerArea = is_null($managerId);

// Deny access to manager area
if (cannotAccessManagerArea) {
    die('Only managers are allowed access to the manager area');
}
```

In practice you may like to create a helper class to help with keeping the authentication process more readable

```
class Auth {

    /**
     * Returns the user authenticated to a namespace
     * @param string $namespace
     * @return \App\Models\Users\User
     */
    public static function getUser($namespace) {
        return \Sinevia\Authentication::getInstance()->getIdentity($namespace);
    }

    /**
     * Sets the authenticated user to a namespace
     * @param string $namespace
     * @param \App\Models\Users\User $user
     * @return void
     */
    public static function setUser($namespace, $user) {
        return \Sinevia\Authentication::getInstance()->setIdentity($namespace, $user);
    }

    /**
     * Removes the user from a namespace
     * @param string $namespace
     * @return void
     */
    public static function removeUser($namespace) {
        return \Sinevia\Authentication::getInstance()->emptyIdentity($namespace);
    }
}
```

Then use it like:

```
$managerId = Auth::getUser('manager');
```
