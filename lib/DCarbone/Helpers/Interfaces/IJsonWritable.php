<?php namespace DCarbone\Helpers\Interfaces;

use DCarbone\Helpers\JsonWriterPlus;

/**
 * Class IJsonWritable
 * @package DCarbone\Helpers\Interfaces
 */
interface IJsonWritable
{
    /**
     * @param JsonWriterPlus $writer
     * @param IJsonWritable $data
     * @return mixed
     */
    public function buildJson(JsonWriterPlus &$writer, IJsonWritable &$data = null);
}