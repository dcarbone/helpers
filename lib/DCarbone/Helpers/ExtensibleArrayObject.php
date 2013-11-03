<?php namespace DCarbone\Helpers;

/**
 * This class is designed to make it easier to write your own class extension of the base ArrayObject
 * class, as well as adding some features I believe the base class is lacking.
 *
 * Credit for this class goes to several commenters on the official PHP ArrayObject manual page
 *
 * For more information on the base ArrayObject class, see here:
 * @link http://www.php.net/manual/en/class.arrayobject.php
 *
 * Additional inspiration taken from Doctrine's ArrayCollection class
 *
 * @link https://github.com/doctrine/collections/blob/master/lib/Doctrine/Common/Collections/ArrayCollection.php
 *
 * Class ExtensibleArrayObject
 * @package DCarbone\Helpers
 */
class ExtensibleArrayObject extends \ArrayObject
{
    /**
     * @var array
     */
    private $_propertyKeys = array();

    /**
     * @var int
     */
    private $_numericalIndex = 0;

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
            $this->_propertyKeys = array_keys($input);
        else if (is_object($input))
            $this->_propertyKeys = array_keys(get_object_vars($input));

        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * Credit for this method goes to php5 dot man at lightning dot hu
     *
     * @link http://www.php.net/manual/en/class.arrayobject.php#107079
     *
     * This method allows you to call any of PHP's built-in array_* methods that would
     * normally expect an array parameter.
     *
     * Example: $myobj = new ExtensibleArrayObject(array('b','c','d','e','a','z')):
     *
     * $myobj->array_keys();  returns array(0, 1, 2, 3, 4, 5)
     *
     * $myobj->array_merge(array('1', '2', '3', '4', '5')); returns array('b','c','d','e','a','z','1','2','3','4,'5');
     *
     * And so on.
     *
     * WARNING:  In utilizing call_user_func_array(), using this method WILL have an adverse affect on performance.
     * I recommend using this method only for development purposes.
     *
     * @param $func
     * @param $argv
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($func, $argv)
    {
        if (!is_callable($func) || substr($func, 0, 6) !== 'array_')
            throw new \BadMethodCallException(__CLASS__.'->'.$func);

        return call_user_func_array($func, array_merge(array($this->getArrayCopy()), $argv));
    }

    /**
     * Return the keys present on this object
     *
     * This is a quick helper method to assist IDE's with intellisense.
     *
     * @return array
     */
    public function array_keys()
    {
        return $this->_propertyKeys;
    }

    /**
     * @param callable $func
     * @return static
     */
    public function map(\Closure $func)
    {
        return new static(array_map($func, $this->getArrayCopy()));
    }

    /**
     * @param callable $closure
     * @return static
     */
    public function filter(\Closure $closure)
    {
        return new static(array_map($closure, $this->getArrayCopy()));
    }

    /**
     * @param $value
     * @return bool
     */
    public function contains($value)
    {
        foreach($this as $k=>$v)
        {
            if ($value === $v)
                return true;
        }

        return false;
    }

    /**
     * @param callable $closure
     * @return bool
     */
    public function exists(\Closure $closure)
    {
        foreach($this as $k=>$v)
        {
            if ($closure($k, $v))
                return true;
        }

        return false;
    }

    /**
     * @param mixed $index
     * @param mixed $value
     */
    public function set($index, $value)
    {
        parent::offsetSet($index, $value);

        if (!in_array($index, $this->_propertyKeys, true))
            $this->_propertyKeys[] = ($index === null ? $this->_numericalIndex++ : $index);
    }

    /**
     * @param mixed $value
     */
    public function append($value)
    {
        $this->set(null, $value);
    }

    /**
     * @param mixed $index
     * @param mixed $newval
     */
    public function offsetSet($index, $newval)
    {
        $this->set($index, $newval);
    }

    /**
     * @param $index
     * @return mixed
     */
    public function remove($index)
    {
        $idx = array_search($index, $this->_propertyKeys, true);

        if ($idx === false)
            return null;

        $removed = $this[$index];

        parent::offsetUnset($index);
        unset($this->_propertyKeys[$idx]);

        return $removed;
    }

    /**
     * @param mixed $index
     */
    public function offsetUnset($index)
    {
        $this->remove($index);
    }

    /**
     * @param $index
     * @return bool
     */
    public function containsKey($index)
    {
        return in_array($index, $this->_propertyKeys, true);
    }

    /**
     * @param mixed $index
     * @return bool
     */
    public function offsetExists($index)
    {
        return $this->containsKey($index);
    }

    /**
     * Does this ArrayObject contain elements?
     *
     * @return bool
     */
    public function isEmpty()
    {
        return (count($this) === 0);
    }

    /**
     * Get first element
     *
     * @return mixed
     */
    public function first()
    {
        if (!$this->isEmpty())
            return $this[reset($this->_propertyKeys)];

        return null;
    }

    /**
     * Get last element
     *
     * @return null
     */
    public function last()
    {
        if (!$this->isEmpty())
            return $this[end($this->_propertyKeys)];

        return null;
    }
}