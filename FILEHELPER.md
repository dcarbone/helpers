## FileHelper

### File Line Counting

Counting the number of lines in a file is something that has always been a bit awkward in PHP, either requiring you to loop
through the file line by line and iterating some integer yourself or scooping the whole file into an array in memory and counting the array.

Neither of these are particularly fast or elegant solutions.

To help rectify this, this helper executes a [exec()](http://php.net/manual/en/function.exec.php) command as to
allow the OS itself to provide the line count.  This is a MUCH faster, much more memory-sensitive solution to this problem.

**Usage**

```php
$length = \DCarbone\Helpers\FileHelper::getLineCount(realpath(__DIR__).'/../FILEHELPER.md');

var_dump($length);
```

**Systems**

This library was designed to work with most Windows and Linux OS's, and as such comes with an argument for each:

- Linux: ``` 'wc -l "{FILE_PATH}"' ```
- Windows: ``` 'findstr /R /N "^" "{FILE_PATH}" | find /c ":"' ```

The string ``` {FILE_PATH} ``` will be replaced at execution time with the path you provided to the ```FileHelper::getLineCount```
method.

By default the method will attempt to determine the system it is on based on the globally defined constant
``` DIRECTORY_SEPARATOR ```.

If you wish to override or add your own "system" and accompanying command, you may do so with the ``` addLineCountCommand ```
method.

**Adding a system**

The term "system" is fairly loosely defined here, and is mostly for convenience.  If you wish to have the helper count
something other than lines, you could do something like this:

```php
\DCarbone\Helpers\FileHelper::addLineCountCommand('linux-bytes', 'wc -c "{FILE_PATH}"');

$bytes = \DCarbone\Helpers\FileHelper::getLineCount(realpath(__DIR__).'/../FILEHELPER.md', 'linux-bytes');

var_dump($bytes);
```

The important piece is the ``` "{FILE_PATH}" ``` bit of the command, it is required.

### Other Methods!

Right now there aren't any!  But, as need arises (or as suggestions come in) I will be expanding
the capabilities of this helper.
