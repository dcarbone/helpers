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
     * @var array
     */
    protected $propertyKeys = array();

    /**
     * Constructor
     *
     * @param array $input
     * @param int $flags
     * @param string $iterator_class
     */
    public function __construct($input = array(), $flags = 0, $iterator_class = 'ArrayIterator')
    {
        if (is_array($input))
            $this->propertyKeys = array_keys($input);
        else if (is_object($input))
            $this->propertyKeys = get_object_vars($input);

        parent::__construct($input, $flags, $iterator_class);
    }

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
     * @param null $index
     * @return mixed
     */
    public function append($value, $index = null)
    {
        $this->offsetSet($index, $value);
    }

    /**
     * @param mixed $index
     * @param mixed $newval
     */
    public function offsetSet($index, $newval)
    {
        parent::offsetSet($index, $newval);

        if (!in_array($index, $this->propertyKeys, true))
            $this->propertyKeys[] = $index;
    }

    /**
     * @param mixed $index
     */
    public function offsetUnset($index)
    {
        parent::offsetUnset($index);
        $idx = array_search($index, $this->propertyKeys, true);
        if ($idx !== false)
            unset($this->propertyKeys[$idx]);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return (count($this) === 0);
    }

    /**
     * @return mixed
     */
    public function first()
    {
        if (!$this->isEmpty() && count($this->propertyKeys) > 0)
            return $this[reset($this->propertyKeys)];

        return null;
    }
}