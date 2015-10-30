<?php namespace DCarbone\Helpers;

/**
 * Class SystemOSTypeHelper
 * @package DCarbone\Helpers
 */
class SystemOSTypeHelper
{
    /**
     * @return string
     */
    public function __invoke()
    {
        return static::invoke();
    }

    /**
     * @return string
     */
    public static function invoke()
    {
        $sysname = strtolower(php_uname('s'));

        if (0 === strpos($sysname, 'win'))
            return 'windows';

        return 'non-windows';
    }
}