## SilentCommand

It can sometimes be useful to execute a command in the background from another thread, however the
mechanisms to do this are not obvious.

To that end, I have created this little helper class which will create **.sh** or **.bat** script files,
depending on the system you are running, which it then executes as background tasks.

The tmp files are also self-deleting if all went well.  If there was an error during execution,
you might end up with .sh and .bat files cluttering up your filesystem.

Keep in mind that silent commands mean silent success, but also silent failure.  I would recommend
heavily testing any piece of code that utilizes this helper to ensure that you both know how the
helper functions and that it delivers the expected results.

### Basic Usage

```php
$path = realpath(__DIR__).DIRECTORY_SEPARATOR.'test.html';

$phpCommand = "file_put_contents('".$path."', '<h1>It works!</h1>');";

\DCarbone\Helpers\SilentCommand::execute('php -r "'.$phpCommand.'"');
```

The above command will attempt to silently place a "test.html" file in the containing script
files current parent directory.

You may pass in either a string or array of strings for the ```$commands``` parameter, and the 2nd
parameter is a bool that determines if a cd to script root command is inserted.  Further explanation below.

### Class Vars

This helper class defines two static variables:

```php
/** @var string */
public static $tmpDir = __DIR__;

/** @var string */
public static $scriptRootDir = null;
```

The first var, ``` $tmpDir ```, is required for the helper to function.  It can be any directory path
string.

The second var, ``` $scriptRootDir ```, is optional.  It can be helpful if you always want to execute
scripts from within a specific directory, or if your app supports CLI commands.  The **cd** command
will always be executed before any other commands.

### Warnings

This helper can be either very useful or very dangerous depending on your specific implementation.  I would
NOT recommend using this helper to execute commands inside of a loop unless you are VERY certain that it is
both necessary and your server can handle the load.

### Improvements

If you have any comments / suggestions on ways to improve this helper's implementation, please let me know.