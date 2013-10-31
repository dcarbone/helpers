<?php namespace DCarbone\Helpers;

if (interface_exists('JsonSerializable'))
{
    /**
     * Class JsonSerializable
     * @package DCarbone\Helpers
     */
    interface JsonSerializable extends \JsonSerializable {}
}
else
{
    /**
     * Class JsonSerializable
     * @package DCarbone\Helpers
     */
    interface JsonSerializable
    {
        /**
         * @return mixed
         */
        public function jsonSerialize();
    }
}