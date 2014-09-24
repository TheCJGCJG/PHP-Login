PHP Login Class
=========

This is a basic PHP login class for authenticating users through a MySQL Database.

  - Usernames
  - Hashed & Salted Passwords


Usage
--------------

```php
//Initialize the class 
require_once 'login.php';
$login = new login("127.0.0.1", "web_login", "TotallySecurePassword", "webSite_Storage", "3306", "storage_")

//Create user Hello with the password of World
$login->createAccount('Hello', 'World');

//Check an account password
if ($login->checkPassword('Hello', 'World')) { 
    //Password is correct
} else {
    //Password is incorrect
}

```

Functions
--------------
- Initialize

```php
/**
* Initialisation is done when instantiating the class like so
*/
$login = new login("127.0.0.1", "web_login", "TotallySecurePassword", "webSite_Storage", "3306", "storage_")
```

- Create A User Account

```php
$login->createAccount(string $username, string $password);

/*
* Returns true on success, returns false on error
*/

```
- Delete a User Account

```php
$login->deleteAccount(string $username);

/*
* Returns true on success, returns false on error
*/

```
- Change a User Account Password

```php
$login->changePassword(string $username, string $password);

/*
* Returns true on success, returns false on failiure.
*/
```
- User Permissions

```php
//TO BE COMPLETED

```
- Password Checking

```php
$login->checkPassword(string $username, string $password);

/*
* Returns true on correct password, returns false on incorrect password
*/
```
