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
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', $encoding);
        return parent::loadHTML($html);
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
        if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 50306)
        {
            return parent::saveHTML($node);
        }
        else
        {
            return preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                    "!</body></html>$!si"),
                "",
                $this->saveHTML($node));
        }
    }
}