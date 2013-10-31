<?php namespace DCarbone\Helpers;

/**
 * Credit for this class goes to sfinktah at php dot spamtrak dot org
 *
 * @link http://www.php.net/manual/en/class.arrayobject.php#103508
 *
 * This class is designed to make it easier to write your own class extension of the base ArrayObject
 * class.
 *
 * For more information on the base ArrayObject class, see here:
 * @link http://www.php.net/manual/en/class.arrayobject.php
 *
 * Class ExtensibleArrayObject
 * @package DCarbone\Helpers
 */
class ExtensibleArrayObject extends \ArrayObject
{
    /**
     * Credit for this method goes to php5 dot man at lightning dot hu
     *
     * @link http://www.php.net/manual/en/class.arrayobject.php#107079
     *
     * @param $func
     * @param $argv
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($func, $argv)
    {
        if (!is_callable($func) || substr($func, 0, 6) !== 'array_')
        {
            throw new \BadMethodCallException(__CLASS__.'->'.$func);
        }
        return call_user_func_array($func, array_merge(array($this->getArrayCopy()), $argv));
    }

    /**
     * @TODO Be warned, the parameters are backwards from what you might expect.
     *
     * This is to allow for downgrades or people already using the base append() method
     *
     * @link http://www.php.net/manual/en/arrayobject.append.php
     *
     * @param mixed $value
     * @param null $name
     * @return mixed
     */
    public function append($value, $name = null)
    {
        $this->offsetSet($name, $value);
    }
}