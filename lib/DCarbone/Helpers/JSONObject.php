<?php namespace DCarbone\Helpers;

/**
 * Class JsonObject
 * @package DCarbone\DCJSONWriter
 */
class JsonObject
{
    /**
     * The data housed in this object
     * @var Mixed
     */
    protected $data = null;

    /**
     * Currently Accessed Element
     * @var Mixed
     */
    protected $current = null;

    /**
     * Parent of the current element
     * @var Mixed
     */
    protected $parent = null;

    /**
     * Most recent key set in an object
     * @var String
     */
    protected $currentObjKey = null;

    /**
     * Keys to get to current element
     * @var array
     */
    protected $pathKeys = array();

    /**
     * Intialize a new object
     *
     * @throws \Exception
     * @return Boolean
     */
    public function startObject()
    {
        $obj = new \stdClass;

        if ($this->current === null && $this->data === null)
        {
            $this->data = $obj;
            $this->current = &$this->data;
            return true;
        }

        if (is_array($this->current))
            return $this->appendToArray($obj);

        if (is_object($this->current))
            return $this->appendToObject($obj);

        // If we reach this point, something weird has happened
        throw new \Exception('In-memory JSON representation is unstable');
    }

    /**
     * "End" object
     *
     * @throws \Exception
     * @return Boolean
     */
    public function endObject()
    {
        if ($this->parent === null)
            return false;

        if (!is_object($this->current))
            throw new \Exception("Cannot end non-object with endObject");

        $this->current = &$this->parent;
        array_pop($this->pathKeys);
        $this->getParent();
        return true;
    }

    /**
     * Initialize Array
     *
     * @throws \Exception
     * @return Boolean
     */
    public function startArray()
    {
        if ($this->current === null && $this->data === null)
        {
            $this->data = array();
            $this->current = &$this->data;
            return true;
        }

        if (is_array($this->current))
            return $this->appendToArray(array());

        if (is_object($this->current))
            return $this->appendToObject(array());

        // If we reach this point, something weird has happened
        throw new \Exception('In-memory JSON representation is unstable');
    }

    /**
     * "End" Array
     *
     * @throws \Exception
     * @return Boolean
     */
    public function endArray()
    {
        if ($this->parent === null)
            return false;

        if (!is_array($this->current))
            throw new \Exception("Cannot end non-array with endArray");

        unset($this->current);
        $this->current = &$this->parent;
        array_pop($this->pathKeys);
        $this->getParent();
        return true;
    }

    /**
     * Append new Array or Object to current Object
     *
     * @param  Mixed $data  Array or Object being appended
     * @throws \Exception
     * @return Boolean
     */
    protected function appendToObject($data)
    {
        if ($this->currentObjKey === null)
            throw new \Exception("Cannot assign value without first assigning key to object");

        $key = $this->currentObjKey;
        $this->current->$key = $data;
        $this->current = &$this->current->$key;
        $this->currentObjKey = null;
        $this->pathKeys[] = $key;
        $this->getParent();
        return true;
    }

    /**
     * Append new Array or Object to current Array
     *
     * @param  Mixed  $data  Array or Object being appended
     * @return Boolean
     */
    protected function appendToArray($data)
    {
        $count = array_push($this->current, $data);
        $this->current = &$this->current[($count-1)];
        $this->pathKeys[] = ($count - 1);
        $this->getParent();
        return true;
    }

    /**
     * Write new property for Object
     *
     * @param  String $propertyName  Name of property
     * @throws \Exception
     * @return Boolean
     */
    public function writeObjectPropertyName($propertyName)
    {
        if (!is_string($propertyName) && !is_int($propertyName))
            throw new \Exception("Can only assign String or Integer values to object property name!");

        if (!is_object($this->current))
            throw new \Exception("Tried to write property value to non-object");

        if ($this->currentObjKey !== null)
            throw new \Exception("Cannot define new property without populating previous one");

        $this->current->$propertyName = null;
        $this->currentObjKey = $propertyName;
        return true;
    }

    /**
     * Write value to Array or Object
     *
     * @param  String $value  Value to write
     * @throws \Exception
     * @return Boolean
     */
    public function writeValue($value)
    {
        if (!is_scalar($value))
            throw new \Exception("Tried to write invalid value");

        if (is_array($this->current))
            return $this->writeValueToArray($value);

        if (is_object($this->current))
            return $this->writeValueToObject($value);

        throw new \Exception('In-memory JSON representation is unstable');
    }

    /**
     * Appends string value to array
     *
     * @param  String  $value  String to write
     * @return Boolean
     */
    protected function writeValueToArray($value)
    {
        $this->current[] = $value;
        return true;
    }

    /**
     * Appends string value to object
     *
     * @param  String $value  Value to write
     * @throws \Exception
     * @return Boolean
     */
    protected function writeValueToObject($value)
    {
        if ($this->currentObjKey === null)
            throw new \Exception("Tried to define value without first defining key");

        $key = $this->currentObjKey;
        $this->current->$key = $value;
        $this->currentObjKey = null;
        return true;
    }

    /**
     * Find the parent of the current element
     *
     * @return Void
     */
    protected function getParent()
    {
        unset($this->parent);

        $parent = &$this->data;

        $count = count($this->pathKeys);

        for($i = 0; $i < ($count - 1); $i++)
        {
            $k = $this->pathKeys[$i];
            if (is_object($parent))
            {
                $parent = &$parent->$k;
            }
            else if (is_array($parent))
            {
                $parent = &$parent[$k];
            }
        }

        $this->parent = &$parent;
    }

    /**
     * Returns the data
     *
     * @return Mixed
     */
    public function getData()
    {
        return $this->data;
    }
}