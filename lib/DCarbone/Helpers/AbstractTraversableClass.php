<?php namespace DCarbone\Helpers;

use DCarbone\Helpers\JsonSerializable;

/**
 * Class AbstractTraversableClass
 * @package DCarbone\Helpers
 */
abstract class AbstractTraversableClass implements \Countable, \RecursiveIterator, \SeekableIterator, \ArrayAccess, \Serializable, JsonSerializable
{
    /**
     * Used by Iterators
     * @var mixed
     */
    private $_position;
    private $_positionKeys = array();
    private $_positionKeysPosition = 0;

    /**
     * @var array
     */
    private $_dataSet = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_dataSet = $data;
        $this->updateKeys();
    }

    /**
     * Credit for this method goes to php5 dot man at lightning dot hu
     *
     * @link http://www.php.net/manual/en/class.arrayobject.php#107079
     *
     * This method allows you to call any of PHP's built-in array_* methods that would
     * normally expect an array parameter.
     *
     * Example: $myobj = new $concreteClass(array('b','c','d','e','a','z')):
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

        return call_user_func_array($func, array_merge(array($this->_dataSet), $argv));
    }

    /**
     * @return array
     */
    public function array_keys()
    {
        return $this->_positionKeys;
    }

    /**
     * echo this object!
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }

    /**
     * make this object an array!
     *
     * @return array
     */
    public function __toArray()
    {
        return $this->_dataSet;
    }

    /**
     * @param $param
     * @return mixed
     * @throws \OutOfRangeException
     */
    public function __get($param)
    {
        if (!isset($this->_dataSet[$param]))
            throw new \OutOfRangeException('No data element with the key "'.$param.'" found');

        return $this->_dataSet[$param];
   }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
        return true;
    }

    /**
     * Append a value
     *
     * @param mixed $value
     * @return bool
     */
    public function append($value)
    {
        $this->offsetSet(null, $value);
        return true;
    }

    /**
     * Remove and return an element
     *
     * @param $index
     * @return mixed|null
     */
    public function remove($index)
    {
        if (!$this->offsetExists($index))
            return null;

        $removed = $this->offsetGet($index);
        $this->offsetUnset($index);
        return $removed;
    }

    /**
     * Is this collection empty?
     *
     * @return bool
     */
    public function isEmpty()
    {
        return ($this->count() === 0);
    }

    /**
     * Return the first item in the dataset
     *
     * @return mixed
     */
    public function first()
    {
        if ($this->isEmpty())
            return null;

        return reset($this->_dataSet);
    }

    /**
     * Return the last element in the dataset
     *
     * @return mixed
     */
    public function last()
    {
        if ($this->isEmpty())
            return null;

        return end($this->_dataSet);
    }

    /**
     * Updates the internal positionKeys value
     *
     * @return void
     */
    private function updateKeys()
    {
        $this->_positionKeys = array_keys($this->_dataSet);
        $this->_position = reset($this->_positionKeys);
        $this->_positionKeysPosition = 0;
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return ($this->_position === null ? false : $this->_dataSet[$this->_position]);
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->_positionKeysPosition++;
        if (isset($this->_positionKeys[$this->_positionKeysPosition]))
            $this->_position = $this->_positionKeys[$this->_positionKeysPosition];
        else
            $this->_position = null;
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->_dataSet[$this->_position]);
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->_positionKeysPosition = 0;
        if (isset($this->_positionKeys[$this->_positionKeysPosition]))
            $this->_position = $this->_positionKeys[$this->_positionKeysPosition];
        else
            $this->_position = null;
    }

    /**
     * (PHP 5 >= 5.1.0)
     * Returns if an iterator can be created for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     */
    public function hasChildren()
    {
        return ($this->valid() && is_array($this->_dataSet[$this->_position]));
    }

    /**
     * (PHP 5 >= 5.1.0)
     * Returns an iterator for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return \RecursiveIterator An iterator for the current entry.
     */
    public function getChildren()
    {
        return $this->_dataSet[$this->_position];
    }

    /**
     * (PHP 5 >= 5.1.0)
     * Seeks to a position
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position <p>
     * The position to seek to.
     * </p>
     * @throws \OutOfBoundsException
     * @return void
     */
    public function seek($position)
    {
        if (!isset($this->_positionKeys[$position]))
            throw new \OutOfBoundsException('Invalid seek position ('.$position.')');

        $this->_positionKeysPosition = $position;
        $this->_position = $this->_positionKeys[$this->_positionKeysPosition];
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return (array_search($offset, $this->_positionKeys, true) !== false);
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset))
            return $this->_dataSet[$offset];
        else
            return null;
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null)
            $this->_dataSet[] = $value;
        else
            $this->_dataSet[$offset] = $value;

        $this->updateKeys();
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @throws \OutOfBoundsException
     * @return void
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset))
            unset($this->_dataSet[$offset]);
        else
            throw new \OutOfBoundsException('Tried to unset undefined offset ('.$offset.')');

        $this->updateKeys();
    }

    /**
     * (PHP 5 >= 5.1.0)
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->_dataSet);
    }

    /**
     * (PHP 5 >= 5.1.0)
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->_dataSet);
    }

    /**
     * (PHP 5 >= 5.1.0)
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->_dataSet = unserialize($serialized);
        $this->_positionKeys = array_keys($this->_dataSet);
        $this->_position = reset($this->_positionKeys);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->_dataSet;
    }
}