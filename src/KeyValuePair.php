<?php namespace DCarbone\Helpers;

use DCarbone\Helpers\JsonSerializable;

/**
 * Class KeyValuePair
 * @package DCarbone\Helpers
 */
class KeyValuePair implements JsonSerializable, \Serializable
{
    /**
     * Key of this object
     * @var Mixed
     */
    public $key;

    /**
     * Value of this object
     * @var Mixed
     */
    public $value;

    /**
     * Constructor
     *
     * @param Mixed $key   Key
     * @param Mixed $value Value
     */
    public function __construct($key, $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Base __toString method
     *
     * @return string
     */
    public function __toString()
    {
        if (is_string($this->value))
            return $this->value;

        if (is_bool($this->value))
        {
            switch($this->value)
            {
                case true : return '(bool)true';
                case false : return '(bool)false';
            }
        }

        if (is_scalar($this->value))
            return (string)$this->value;


        if (is_array($this->value))
            return implode(", ", $this->value);

        $val = $this->value;
        $set = @settype($val, "string");
        if ($set === true)
            return $val;

        return gettype($this->value);
    }

    /**
     * Returns type of value
     *
     * @link  http://php.net/manual/en/function.gettype.php
     * @return String
     */
    public function getType()
    {
        return gettype($this->value);
    }

    /**
     * ------------------------------------------------
     * Serializable Implementation
     * ------------------------------------------------
     */
    public function serialize()
    {
        return serialize(array($this->key => $this->value));
    }
    public function unserialize($data)
    {
        $var = unserialize($data);
        if ($var === false || !is_array($var))
            throw new \InvalidArgumentException('\DCarbone\Helper\KeyValuePair cannot be populated with '.gettype($var));

        $this->key = key($var);
        $this->value = reset($var);
    }

    /**
     * ------------------------------------------------
     * JsonSerializable Implementation
     * ------------------------------------------------
     */
    public function jsonSerialize()
    {
        return array($this->key, $this->value);
    }

    /**
     * PHP 5.3 JsonSerialize hack
     *
     * Whether or not the contents of this object
     * can be converted to a json string is
     * entirely the responsibility of the user
     * of this class
     *
     * @return string json-ized string
     */
    public function __toJson()
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * PHP Laziness Hack
     *
     * Because PHP does not implement a __toArray magic
     * method on its own we must, for now, call it manually
     * ie. $obj->__toArray();
     *
     * @return Array
     */
    public function __toArray()
    {
        return array($this->key, $this->value);
    }

}
