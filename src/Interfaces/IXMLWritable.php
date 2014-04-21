<?php namespace DCarbone\Helpers\Interfaces;

use DCarbone\Helpers\XMLWriterPlus;

/**
 * Class IXMLWritable
 * @package DCarbone\Helpers\Interfaces
 */
interface IXMLWritable
{
    /**
     * @param XMLWriterPlus $xmlWriter
     * @param IXMLWritable $data
     * @return mixed
     */
    public function buildXML(XMLWriterPlus &$xmlWriter, IXMLWritable &$data = null);
}