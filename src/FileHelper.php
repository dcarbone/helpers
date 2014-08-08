<?php namespace DCarbone\Helpers;

/**
 * Class FileHelper
 * @package DCarbone\Helpers
 */
abstract class FileHelper
{
    /**
     * @var array
     */
    protected static $lineCountCommands = array(
        'linux' => 'wc -l "{FILE_PATH}"',
        'windows' => 'findstr /R /N "^" "{FILE_PATH}" | find /c ":"',
    );

    /**
     * @param string $system
     * @param string $commandString
     * @throws \InvalidArgumentException
     */
    public static function addLineCountCommand($system, $commandString)
    {
        if (strpos($commandString, '{FILE_PATH}') === false)
            throw new \InvalidArgumentException(
                'FileHelper::addLineCountCommand - Command for "'.$system.'" ("'.$commandString.'") does not contain'.
                ' required "{FILE_PATH}" string!');

        static::$lineCountCommands[$system] = $commandString;
    }

    /**
     * @return array
     */
    public static function getLineCountCommands()
    {
        return static::$lineCountCommands;
    }

    /**
     * @param string $filePath
     * @param null|string $system
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return int|null
     */
    public static function getLineCount($filePath, $system = null)
    {
        if ($system === null)
            $system = (DIRECTORY_SEPARATOR === '/' ? 'linux' : 'windows');

        if (!is_string($filePath))
            throw new \InvalidArgumentException('FileHelper::getLineCount - "$filePath" expected to be string, saw "'.gettype($filePath).'"');

        if (!file_exists($filePath))
            throw new \InvalidArgumentException('FileHelper::getLineCount - "$filePath" specified non-existent file "'.$filePath.'"');

        if (isset(static::$lineCountCommands[$system]))
            return (int)exec(str_replace('{FILE_PATH}', $filePath, static::$lineCountCommands[$system]));

        throw new \RuntimeException('FileHelper::getLineCount - Could not determine file system type');
    }

    /**
     * Because Windows................. :(
     *
     * @param string $file
     * @return bool
     */
    public static function superUnlink($file)
    {
        if (DIRECTORY_SEPARATOR === '/' || strpos(php_uname(), 'Windows') === false)
            return (bool)unlink($file);

        return shell_exec('DEL /F/Q "'.$file.'"');
    }
}