<?php namespace DCarbone\Helpers\Interfaces;

use DCarbone\Helpers\JsonWriterPlus;

/**
 * Class IJsonWritable
 * @package DCarbone\Helpers\Interfaces
 */
interface IJsonWritable
{
    /**
     * @param JsonWriterPlus $jsonWriter
     * @param IJsonWritable $data
     * @return mixed
     */
    public function buildJson(JsonWriterPlus &$jsonWriter, IJsonWritable &$data = null);
}