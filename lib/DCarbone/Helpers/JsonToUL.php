<?php namespace DCarbone\Helpers;

use DCarbone\Helpers\DOMPlus;

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
     * @return DOMPlus|mixed|string
     */
    public static function invoke($jsonString, $returnDOM = false)
    {
        $dom = new DOMPlus('1.0', 'UTF-8');

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
            return $dom->saveHTML($ul);
        else
            return $dom;
    }

    /**
     * Parse through JSON Object
     *
     * @param \stdClass $object
     * @param \DCarbone\Helpers\DOMPlus
     * @param \DOMElement $parentUL
     * @return \DCarbone\Helpers\DOMPlus
     */
    protected static function objectOutput(\stdClass $object, DOMPlus &$dom, \DOMElement &$parentUL)
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
     * @param \DCarbone\Helpers\DOMPlus
     * @param \DOMElement $parentUL
     * @return \DCarbone\Helpers\DOMPlus
     */
    protected static function arrayOutput(array $array, DOMPlus &$dom, \DOMElement &$parentUL)
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
