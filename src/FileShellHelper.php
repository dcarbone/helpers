<?php namespace DCarbone\Helpers;

/**
 * Class FileShellHelper
 * @package DCarbone\Helpers
 */
class FileShellHelper
{
    /**
     * @var array
     */
    protected static $commands = array(
        'linux-line-count' => 'wc -l "{FILE_PATH}"',
        'windows-line-count' => 'findstr /R /N "^" "{FILE_PATH}" | find /c ":"',
    );

    /**
     * @param string $filePath
     * @param string $command
     * @return mixed
     */
    public function __invoke($filePath, $command)
    {
        return static::executeCommand($filePath, $command);
    }

    /**
     * @param string $name
     * @param string $body
     * @throws \InvalidArgumentException
     */
    public static function addCommand($name, $body)
    {
        if (strpos($body, '{FILE_PATH}') === false)
            throw new \InvalidArgumentException(sprintf(
                '%s::addCommand - Command "%s" with body ("%s") does not contain required "{FILE_PATH}" portion.',
                get_called_class(),
                $name,
                $body
            ));

        static::$commands[$name] = $body;
    }

    /**
     * @param string[] $commands
     */
    public static function addCommands(array $commands)
    {
        foreach($commands as $k=>$v)
        {
            static::addCommand($k, $v);
        }
    }

    /**
     * @return array
     */
    public static function getCommands()
    {
        return static::$commands;
    }

    /**
     * @param string $filePath
     * @param null|string $command
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return int|null
     */
    public static function executeCommand($filePath, $command)
    {
        if (!is_string($filePath))
        {
            throw new \InvalidArgumentException(sprintf(
                '%s::executeCommand - "$filePath" expected to be string, saw "%s"',
                get_called_class(),
                gettype($filePath)
            ));
        }

        if (!file_exists($filePath))
        {
            throw new \InvalidArgumentException(sprintf(
                '%s::executeCommand - Specified file "%s" does not appear to exist',
                get_called_class(),
                $filePath
            ));
        }

        if (!is_readable($filePath))
        {
            throw new \InvalidArgumentException(sprintf(
                '%s::executeCommand - "%s" specified points to un-readable file',
                get_called_class(),
                $filePath
            ));
        }

        if (isset(static::$commands[$command]))
            return exec(str_replace('{FILE_PATH}', $filePath, static::$commands[$command]));

        throw new \RuntimeException(sprintf(
            '%s::executeCommand - Unknown command "%s" requested',
            get_called_class(),
            $command
        ));
    }
}