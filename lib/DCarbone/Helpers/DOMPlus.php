<?php namespace DCarbone\Helpers;

/**
 * This class was heavily inspired by Artem Russakovskii's SmartDOMDocument class
 *
 * @link http://beerpla.net/projects/smartdomdocument-a-smarter-php-domdocument-class/
 */

/**
 * Class DOMPlus
 * @package DCarbone\Helpers
 */
class DOMPlus extends \DOMDocument
{
    /**
     * Persistent error array
     *
     * @var array
     */

    protected static $allLoadErrors = array();
    /**
     * @var array
     */
    protected $loadErrors = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        call_user_func_array('parent::__construct', func_get_args());

        libxml_use_internal_errors(true);
    }

    /**
     * Get the last errors thrown when loading HTML/XML
     *
     * @return array
     */
    public function getLastLoadErrors()
    {
        return $this->loadErrors;
    }

    /**
     * Get all of the errors seen while loading HTML/XML
     *
     * @return array
     */
    public function getAllLoadErrors()
    {
        return static::$allLoadErrors;
    }

    /**
     * Load HTML with a proper encoding fix/hack.
     * Borrowed from the link below.
     *
     * @link http://www.php.net/manual/en/domdocument.loadhtml.php
     *
     * @param string $html
     * @param string $encoding
     * @return bool
     */
    public function loadHTML($html, $encoding = 'UTF-8')
    {
        libxml_clear_errors();

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);
        $return = parent::loadHTML($html);

        $this->loadErrors = libxml_get_errors();
        static::$allLoadErrors = array_merge(static::$allLoadErrors, $this->loadErrors);

        return $return;
    }

    /**
     * If we are using an older version PHP, the DOMDocument object does not support
     * the ability to export a specific Element's HTML.
     *
     * This little hack allows us to do just that.
     *
     * @param \DOMNode $node
     * @return string
     */
    public function saveHTML(\DOMNode $node = null)
    {
        $this->formatOutput = true;
        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50306)
        {
            return parent::saveHTML($node);
        }
        else if ($node !== null && (!defined('PHP_VERSION_ID') || (defined('PHP_VERSION_ID') && PHP_VERSION_ID < 50306)))
        {
            $newDom = new DOMPlus();
            $newDom->appendChild($newDom->importNode($node->cloneNode(true), true));
            return $newDom->saveHTML();
        }
        else
        {
            return parent::saveHTML();
        }
    }

    /**
     * Return HTML while stripping the annoying auto-added <html>, <body>, and doctype.
     *
     * @link http://php.net/manual/en/migration52.methods.php
     *
     * @param \DOMNode $node
     * @return string
     */
    public function saveHTMLExact(\DOMNode $node = null)
    {
        $this->formatOutput = true;

        if ($node !== null && defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50306)
        {
            return parent::saveHTML($node);
        }
        else
        {
            return preg_replace(array("/^\<\!DOCTYPE.*?<body>/si",
                    "!</body>.*</html>$!si"),
                "",
                $this->saveHTML($node));
        }
    }
}