<?php namespace DCarbone\Helpers;

/**
 * Class FileHelper
 * @package DCarbone\Helpers
 */
abstract class FileHelper
{
    /**
     * @param string $file_path
     * @param null|string $system
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return int|null
     */
    public static function getLineCount($file_path, $system = null)
    {
        if ($system === null)
            $system = (DIRECTORY_SEPARATOR === '/' ? 'linux' : 'windows');

        if (!is_string($file_path))
            throw new \InvalidArgumentException('FileHelper::getLineCount - "$file_path" expected to be string, saw "'.gettype($file_path).'"');

        if (!file_exists($file_path))
            throw new \InvalidArgumentException('FileHelper::getLineCount - "$file_path" specified non-existent file "'.$file_path.'"');

        switch($system)
        {
            case 'linux' :
                return (int)exec('wc -l '.$file_path);

            case 'windows' :
                return (int)exec('findstr /R /N "^" '.$file_path.' | find /c ":"');
        }

        throw new \RuntimeException('FileHelper::getLineCount - Could not determine file system type');
    }
}