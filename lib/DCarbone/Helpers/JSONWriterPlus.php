<?php namespace DCarbone\Helpers;

/**
 * Class JSONWriterPlus
 * @package DCarbone\Helpers
 */
class JSONWriterPlus
{
    /**
     * str_replace search value(s)
     * @var array
     */
    public $strSearchCharacters = array(
        "&#169;",
        "&#xa9;",
        "&copy;"
    );

    /**
     * str_replace replace value(s)
     * @var array
     */
    public $strReplaceCharacters = array(
        "\u00A9", // copyright symbol
        "\u00A9",
        "\u00A9"
    );

    /**
     * RegExp search value(s)
     * @var array
     */
    public $regexpSearchCharacters = array();

    /**
     * RegExp replace value(s)
     * @var array
     */
    public $regexpReplaceCharacters = array();

    /**
     * JSONObject Instance
     * @var JSONObject
     */
    protected $writer = null;

    /**
     * Has the output been initialized?
     * @var boolean
     */
    protected $started = false;

    /**
     * Has the output been finalized?
     * @var boolean
     */
    protected $ended = false;

    /**
     * Iterates Comments
     * @var integer
     */
    protected $commentCount = 0;

    /**
     * Quick helper function to determine if this object
     * is editable
     *
     * @access  public
     * @return  Boolean
     */
    protected function canEdit()
    {
        return ($this->started === true && $this->ended === false);
    }

    /**
     * Append a new object to the JSON output
     *
     * @return  Boolean
     */
    public function writeStartObject()
    {
        if ($this->canEdit())
            return $this->writer->startObject();

        return false;
    }

    /**
     * End current object
     *
     * @return  Boolean
     */
    public function writeEndObject()
    {
        if ($this->canEdit())
            return $this->writer->endObject();

        return false;
    }

    /**
     * Append new Array
     *
     * @return  Boolean
     */
    public function writeStartArray()
    {
        if ($this->canEdit())
            return $this->writer->startArray();

        return false;
    }

    /**
     * End current Array
     *
     * @return  Boolean
     */
    public function writeEndArray()
    {
        if ($this->canEdit())
            return $this->writer->endArray();

        return false;
    }

    /**
     * Write object property
     *
     * @param   String  $property  Property Name
     * @return  Boolean
     */
    public function writeObjectPropertyName($property)
    {
        if ($this->canEdit() && (is_string($property) || is_int($property)))
            return $this->writer->writeObjectPropertyName($property);

        return false;
    }

    /**
     * Defines a property with value of string
     *
     * @param   String  $property  Name of Property
     * @param   String  $value     Value of Property
     * @return  Bool
     */
    public function writeObjectProperty($property, $value)
    {
        if ($this->canEdit())
        {
            return $this->writeObjectPropertyName($property) &&
            $this->writeValue($value);
        }

        return false;
    }

    /**
     * Write String Value to Array or Object
     *
     * @param   String $value  Value
     * @throws \InvalidArgumentException
     * @return  Boolean
     */
    public function writeValue($value)
    {
        if ($this->canEdit())
        {
            // If a non-scalar value is passed in (such as a class object)
            // try to convert it to string.  At this point, writeValue MUST be scalar!
            if (!is_scalar($value))
            {
                $typecast = settype($value, 'string');

                if ($typecast === false)
                    throw new \InvalidArgumentException("Cannot cast non-scalar value to string (did you forget to define a __toString on your object?)");
            }

            if (is_string($value))
            {
                $value = $this->convertCharacters($value);
                $value = $this->encodeString($value);
            }

            return $this->writer->writeValue($value);
        }

        return false;
    }

    /**
     * Initialize Data
     *
     * @return  Boolean
     */
    public function startJSON()
    {
        if (!$this->started)
        {
            $this->writer = new JSONObject;
            return $this->started = true;
        }
        return false;
    }

    /**
     * End current JSONObject editing
     *
     * @return  Boolean
     */
    public function endJSON()
    {
        if (!$this->ended)
            return $this->ended = true;

        return false;
    }

    /**
     * Get JSON string from contents;
     *
     * @link  http://php.net/manual/en/function.json-encode.php
     * @link  http://www.php.net/manual/en/json.constants.php
     *
     * @param   Int $options  json_encode options
     * @throws \Exception
     * @return  String
     */
    public function getJSON($options = 0)
    {
        if (!is_int($options))
            throw new \Exception("Cannot pass non-int value to GetJSON");

        return json_encode($this->writer->getData(), $options);
    }

    /**
     * Get the unencoded value of the writer
     *
     * @return Mixed
     */
    public function getUnencoded()
    {
        return $this->writer->getData();
    }

    /**
     * Convert characters for output in an XML file
     *
     * @param   String $string  Input String
     * @throws \InvalidArgumentException
     * @return  String
     */
    protected function convertCharacters($string)
    {
        $strSearch = null;
        $strReplace = null;
        $regexpSearch = null;
        $regexpReplace = null;

        // See if we have str_replace keys
        if ((is_string($this->strSearchCharacters) && $this->strSearchCharacters !== "") ||
            (is_array($this->strSearchCharacters) && count($this->strSearchCharacters) > 0))
        {
            $strSearch = $this->strSearchCharacters;
        }

        // If we have search keys, see if we have replace keys
        if ($strSearch !== null &&
            (is_string($this->strReplaceCharacters) && $this->strReplaceCharacters !== "") ||
            (is_array($this->strReplaceCharacters) && count($this->strReplaceCharacters) > 0))
        {
            $strReplace = $this->strReplaceCharacters;
        }

        // See if we have preg_replace keys
        if ((is_string($this->regexpSearchCharacters) && $this->regexpSearchCharacters !== "") ||
            (is_array($this->regexpSearchCharacters) && count($this->regexpSearchCharacters) > 0))
        {
            $regexpSearch = $this->regexpSearchCharacters;
        }

        // If we have search keys, see if we have replace keys
        if ($regexpSearch !== null &&
            (is_string($this->regexpReplaceCharacters) && $this->regexpReplaceCharacters !== "") ||
            (is_array($this->regexpReplaceCharacters) && count($this->regexpReplaceCharacters) > 0))
        {
            $regexpReplace = $this->regexpReplaceCharacters;
        }

        // Execute str_replace
        if ($strSearch !== null && $strReplace !== null)
            $string = str_replace($strSearch, $strReplace, $string);

        // Execute preg_replace
        if ($regexpSearch !== null && $regexpReplace !== null)
            $string = preg_replace($regexpSearch, $regexpReplace, $string);

        return $string;
    }

    /**
     * Apply requested encoding type to string
     *
     * @link  http://php.net/manual/en/function.mb-detect-encoding.php
     * @link  http://www.php.net/manual/en/function.mb-convert-encoding.php
     *
     * @param   String $string  un-encoded string
     * @throws \InvalidArgumentException
     * @return  String
     */
    protected function encodeString($string)
    {
        $detect = mb_detect_encoding($string);

        if ($detect === false)
            throw new \InvalidArgumentException("Could not convert string to UTF-8 for JSON output");

        // If the current encoding is already the requested encoding
        if (is_string($detect) && strtolower($detect) === "utf-8")
            return $string;

        // Else, perform encoding conversion
        return mb_convert_encoding($string, "UTF-8", $detect);
    }
} 