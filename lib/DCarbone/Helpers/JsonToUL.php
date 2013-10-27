<?php namespace DCarbone\Helpers;

/**
 * Class JsonToUL
 * @package DCarbone
 */
class JsonToUL
{
    /**
     * Invoke the helper
     *
     * @param $jsonString
     * @param bool $returnDOM
     * @return \DOMDocument|mixed|string
     */
    public static function invoke($jsonString, $returnDOM = false)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $jsonString = mb_convert_encoding($jsonString, 'UTF-8', mb_detect_encoding($jsonString));

        $jsonDecode = json_decode($jsonString);

        $ul = $dom->createElement('ul');
        $ul->setAttribute('class', 'json-list');
        $dom->appendChild($ul);

        if ($jsonDecode instanceof \stdClass)
            self::objectOutput($jsonDecode, $dom, $ul);
        else if (is_array($jsonDecode))
            self::arrayOutput($jsonDecode, $dom, $ul);

        if ($returnDOM === false)
        {
            if (PHP_VERSION_ID >= 50306)
                return $dom->saveHTML($ul);
            else
                return preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                        "!</body></html>$!si"),
                    "",
                    $dom->saveHTML());
        }
        else
        {
            return $dom;
        }
    }

    /**
     * Parse through JSON Object
     *
     * @param \stdClass $object
     * @param \DOMDocument $dom
     * @param \DOMElement $parentUL
     * @return \DOMDocument
     */
    protected static function objectOutput(\stdClass $object, \DOMDocument &$dom, \DOMElement &$parentUL)
    {
        foreach($object as $k=>$v)
        {
            $li = $dom->createElement('li');
            $parentUL->appendChild($li);
            $strong = $dom->createElement('strong', $k);
            $li->appendChild($strong);
            if ($v instanceof \stdClass)
            {
                $ul = $dom->createElement('ul');
                $li->appendChild($ul);
                self::objectOutput($v, $dom, $ul);
            }
            else if (is_array($v))
            {
                $ul = $dom->createElement('ul');
                $li->appendChild($ul);
                self::arrayOutput($v, $dom, $ul);
            }
            else if (is_string($v))
            {
                $span = $dom->createElement('span');
                $span->appendChild($dom->createTextNode(' '.$v));
                $li->appendChild($span);
            }
        }
        return $dom;
    }

    /**
     * Parse through JSON Array
     *
     * @param array $array
     * @param \DOMDocument $dom
     * @param \DOMElement $parentUL
     * @return \DOMDocument
     */
    protected static function arrayOutput(array $array, \DOMDocument &$dom, \DOMElement &$parentUL)
    {
        foreach($array as $value)
        {
            $li = $dom->createElement('li');
            $parentUL->appendChild($li);
            if ($value instanceof \stdClass)
            {
                $ul = $dom->createElement('ul');
                $li->appendChild($ul);
                self::objectOutput($value, $dom, $ul);
            }
            else if (is_array($value))
            {
                $ul = $dom->createElement('ul');
                $li->appendChild($ul);
                self::arrayOutput($value, $dom, $ul);
            }
            else if (is_string($value))
            {
                $li->nodeValue = $value;
            }
        }
        return $dom;
    }
}
