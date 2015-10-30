<?php namespace DCarbone\Helpers;

/**
 * Class JsonToList
 * @package DCarbone
 */
class JsonToList
{
    /**
     * @param string $jsonString
     * @param bool $returnNode
     * @return \DOMNode|string
     */
    public function __invoke($jsonString, $returnNode = false)
    {
        return static::invoke($jsonString, $returnNode);
    }

    /**
     * Invoke the helper
     *
     * @param $jsonString
     * @param bool $returnNode
     * @return \DOMNode|string
     */
    public static function invoke($jsonString, $returnNode = false)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        $jsonString = mb_convert_encoding($jsonString, 'UTF-8', mb_detect_encoding($jsonString));

        $jsonDecode = json_decode($jsonString, false);
        $lastError = json_last_error();

        if (JSON_ERROR_NONE === $lastError)
        {
            $ul = $dom->createElement('ul');
            $ul->setAttribute('class', 'json-list');
            $dom->appendChild($ul);

            if ($jsonDecode instanceof \stdClass)
                self::objectOutput($jsonDecode, $dom, $ul);
            else if (is_array($jsonDecode))
                self::arrayOutput($jsonDecode, $dom, $ul);

            if ($returnNode)
                return $dom;

            return static::saveHTMLExact($dom, $ul);
        }

        throw new \DomainException(sprintf(
            'Could not convert input to HTML: "%s"',
            JsonErrorHelper::invoke(true, $lastError)
        ));
    }

    /**
     * @param \DOMDocument $dom
     * @param \DOMNode $node
     * @return string
     */
    protected static function saveHTMLExact(\DOMDocument $dom, \DOMNode $node)
    {
        if ($dom !== null && defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50306)
            return $dom->saveHTML($node);

        return preg_replace(array("/^<!DOCTYPE.*?<body>/si", "#</body>.*</html>$#si"), '', $dom->saveHTML());
    }

    /**
     * Parse through json object
     *
     * @param \stdClass $object
     * @param \DOMDocument $dom
     * @param \DOMElement $parentUL
     * @return \DOMDocument
     */
    protected static function objectOutput(\stdClass $object, \DOMDocument $dom, \DOMElement $parentUL)
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
            else if (is_bool($v))
            {
                switch($v)
                {
                    case true: $li->appendChild($dom->createTextNode(' TRUE')); break;
                    case false: $li->appendChild($dom->createTextNode(' FALSE')); break;
                }
            }
            else if (is_scalar($v))
            {
                $span = $dom->createElement('span');
                $span->appendChild($dom->createTextNode(' '.strval($v)));
                $li->appendChild($span);
            }
        }
        return $dom;
    }

    /**
     * Parse through json array
     *
     * @param array $array
     * @param \DOMDocument $dom
     * @param \DOMElement $parentUL
     * @return \DOMDocument
     */
    protected static function arrayOutput(array $array, \DOMDocument $dom, \DOMElement $parentUL)
    {
        foreach($array as $v)
        {
            $li = $dom->createElement('li');
            $parentUL->appendChild($li);
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
            else if (is_bool($v))
            {
                switch($v)
                {
                    case true: $li->appendChild($dom->createTextNode(' TRUE')); break;
                    case false: $li->appendChild($dom->createTextNode(' FALSE')); break;
                }
            }
            else if (is_scalar($v))
            {
                $li->appendChild($dom->createTextNode(strval($v)));
            }

        }
        return $dom;
    }
}