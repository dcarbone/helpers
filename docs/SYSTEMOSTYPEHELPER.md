## System OS Type Helper

This is an EXTREMELY simple helper that basically allows you to determine "am I on Windows or not".

Future versions might be more complicated, but...probably not.

### Usage

```php
$windows = \DCarbone\Helpers\SystemOSTypeHelper::invoke() === 'windows';
var_dump($windows);
```