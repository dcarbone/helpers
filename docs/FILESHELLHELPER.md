## FileShellHelper

It can often be useful to execute a system binary in lieu of trying to do something in PHP directly.

To that end, this helper is designed to store and allow execution of system commands that interact
with files. It ultimately executes [exec()](http://php.net/manual/en/function.exec.php).

### Basic Usage

**Adding Single Command**

```php
use DCarbone\Helpers\FileShellHelper;
FileShellHelper::addCommand('command-name', 'command  body')
```

_NOTE_: Command bodies MUST include ` {FILE_PATH} ` where it makes sense per that particular command.
It will be replaced with the passed in filepath upon execution.

**Adding Multiple Commands**

```php
FileShellHelper::addCommands(array(
    'command-name' => 'command body',
    'command2-name' => 'command2 body',
));
```

**Executing Commands**

```php
$result = FileShellHelper::executeCommand('filepath to file', 'command-name');
```

OR

```php
$helper = new FileShellHelper;
$result = $helper('filepath to file', 'command-name');
```

**Getting List of Available Commands**

```php
var_dump(FileShellHelper::getCommands());
```

### Predefined Actions

This helper comes with a few pre-defined actions that I find useful.  If you have a command that you feel
is useful, please create a pull request with the updated command array.

#### Current List of Predefined Commands

- [linux-line-count](../src/FileShellHelper.php#L13)
- [windows-line-count](../src/FileShellHelper.php#L14)
