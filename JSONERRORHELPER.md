## JsonErrorHelper

This little helper is designed to deliver human-readable strings from the integers
returned via [json_last_error\(\)](http://php.net/manual/en/function.json-last-error.php).

### Usage

You may invoke this helper with no parameters if you wish.  It will internally
execute [json_last_error\(\)](http://php.net/manual/en/function.json-last-error.php).

```php
use DCarbone\Helpers\JsonErrorHelper;

$msg = JsonErrorHelper::invoke();
```

If you wish to have the name of the ` JSON_ERROR_* ` constant associated with that error:

```php
$msg = JsonErrorHelper::invoke(true);
```

If you already have the integer representing the error:

```php
$int = json_last_error();
$msg = JsonErrorHelper::invoke(false, $int);
```

If you wish to just pass in random integers:

```php
$msg = JsonErrorHelper::invoke(true, 42);
```