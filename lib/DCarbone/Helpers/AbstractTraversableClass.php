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
    private $_position = null;
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
        if (!$this->offsetExists($param))
            throw new \OutOfRangeException('No data element with the key "'.$param.'" found');

        return $this->_dataSet[$param];
   }

    /**
     * This method was inspired by Zend Framework 2.2.x PhpReferenceCompatibility class
     *
     * @link https://github.com/zendframework/zf2/blob/release-2.2.6/library/Zend/Stdlib/ArrayObject/PhpReferenceCompatibility.php#L179
     *
     * @param $dataSet
     * @return array
     * @throws \InvalidArgumentException
     */
    public function exchangeArray($dataSet)
    {
        if (!is_array($dataSet) && !is_object($dataSet))
            throw new \InvalidArgumentException('AbstractTraversableClass::exchangeArray - "$dataSet" parameter expected to be array or object');

        if ($dataSet instanceof \ArrayObject)
            $dataSet = $dataSet->getArrayCopy();
        else if ($dataSet instanceof static)
            $dataSet = $dataSet->__toArray();
        else if (!is_array($dataSet))
            $dataSet = (array)$dataSet;

        $storage = $this->_dataSet;
        $this->_dataSet = $dataSet;
        $this->updateKeys();
        return $storage;
    }


    /**
     * Set a value on this collection
     *
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
     * Try to determine if an identical element is already in this collection
     *
     * @param mixed $element
     * @return bool
     */
    public function contains($element)
    {
        return in_array($element, $this->_dataSet, true);
    }

    /**
     * Custom "contains" method
     *
     * @param callable $func
     * @return bool
     */
    public function exists(\Closure $func)
    {
        foreach($this->_dataSet as $key=>$element)
        {
            if ($func($key, $element))
                return true;
        }

        return false;
    }

    /**
     * Return index of desired key
     *
     * @param mixed $value
     * @return mixed
     */
    public function indexOf($value)
    {
        return array_search($value, $this->_dataSet, true);
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
     * @param $element
     * @return bool
     */
    public function removeElement($element)
    {
        $key = array_search($element, $this->_dataSet, true);

        if ($key === false)
            return false;

        $this->offsetUnset($key);
        return true;
    }

    /**
     * Get an Iterator instance for this data set
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_dataSet);
    }

    /**
     * Applies array_map to this dataset, and returns a new object.
     *
     * @link http://us1.php.net/array_map
     *
     * They scope "static" is used so that an instance of the extended class is returned.
     *
     * @param callable $func
     * @return static
     */
    public function map(\Closure $func)
    {
        return new static(array_map($func, $this->_dataSet));
    }

    /**
     * Filter internal dataset using closure
     *
     * @param callable $func
     * @return bool
     */
    public function filter(\Closure $func)
    {
        foreach($this->_dataSet as $key=>$element)
        {
            if (!$func($key, $element))
                return false;
        }

        return true;
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
     * Sort values by standard PHP sort method
     *
     * @link http://www.php.net/manual/en/function.sort.php
     *
     * @param int $flags
     * @return bool
     */
    public function sort($flags = SORT_REGULAR)
    {
        $sort = sort($this->_dataSet, $flags);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Reverse sort values
     *
     * @link http://www.php.net/manual/en/function.rsort.php
     *
     * @param int $flags
     * @return bool
     */
    public function rsort($flags = SORT_REGULAR)
    {
        $sort = rsort($this->_dataSet, $flags);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Sort values by custom function
     *
     * @link http://www.php.net/manual/en/function.usort.php
     *
     * @param string|array $func
     * @return bool
     */
    public function usort($func)
    {
        $sort = usort($this->_dataSet, $func);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Sort by keys
     *
     * @link http://www.php.net/manual/en/function.ksort.php
     *
     * @param int $flags
     * @return bool
     */
    public function ksort($flags = SORT_REGULAR)
    {
        $sort = ksort($this->_dataSet, $flags);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Reverse sort by keys
     *
     * @link http://www.php.net/manual/en/function.krsort.php
     *
     * @param int $flags
     * @return bool
     */
    public function krsort($flags = SORT_REGULAR)
    {
        $sort = krsort($this->_dataSet, $flags);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Sort by keys with custom function
     *
     * http://www.php.net/manual/en/function.uksort.php
     *
     * @param string|array $func
     * @return bool
     */
    public function uksort($func)
    {
        $sort = uksort($this->_dataSet, $func);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Sort values while retaining indices.
     *
     * @link http://www.php.net/manual/en/function.asort.php
     *
     * @param int $flags
     * @return bool
     */
    public function asort($flags = SORT_REGULAR)
    {
        $sort = asort($this->_dataSet, $flags);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Reverse sort values while retaining indices
     *
     * @link http://www.php.net/manual/en/function.arsort.php
     *
     * @param int $flags
     * @return bool
     */
    public function arsort($flags = SORT_REGULAR)
    {
        $sort = arsort($this->_dataSet, $flags);
        $this->updateKeys();
        return $sort;
    }

    /**
     * Sort values while preserving indices with custom function
     *
     * @link http://www.php.net/manual/en/function.uasort.php
     *
     * @param $func
     * @return bool
     */
    public function uasort($func)
    {
        $sort = uasort($this->_dataSet, $func);
        $this->updateKeys();
        return $sort;
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
        return (!isset($this->_position) || $this->_position === null ? false : $this->_dataSet[$this->_position]);
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
        if (array_key_exists($this->_positionKeysPosition, $this->_positionKeys))
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
        return array_key_exists($this->_position, $this->_dataSet);
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
        if (array_key_exists($this->_positionKeysPosition, $this->_positionKeys))
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
     * @param int $position The position to seek to.
     * 
     * @throws \OutOfBoundsException
     * @return void
     */
    public function seek($position)
    {
        if (!array_key_exists($position, $this->_positionKeys))
            throw new \OutOfBoundsException('Invalid seek position ('.$position.')');

        $this->_positionKeysPosition = $position;
        $this->_position = $this->_positionKeys[$this->_positionKeysPosition];
    }

    /**
     * (PHP 5 >= 5.0.0)
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset An offset to check for.
     * 
     * @return boolean true on success or false on failure.
     * 
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
     * @param mixed $offset The offset to retrieve.
     * 
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
     * @param mixed $offset The offset to assign the value to.
     * 
     * @param mixed $value The value to set.
     * 
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
     * @param mixed $offset The offset to unset.
     * 
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
     * 
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
     * @param string $serialized The string representation of the object.
     * 
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