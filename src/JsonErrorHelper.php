<?php namespace DCarbone\Helpers;

/**
 * Class JsonErrorHelper
 * @package DCarbone\Helpers
 */
class JsonErrorHelper
{
    /**
     * @param bool $includeErrorName
     * @param int|null $lastError
     * @return string
     */
    public function __invoke($includeErrorName = false, $lastError = null)
    {
        return static::invoke($includeErrorName, $lastError);
    }

    /**
     * @param bool|false $includeErrorName
     * @param null $lastError
     * @return string
     */
    public static function invoke($includeErrorName = false, $lastError = null)
    {
        if (null === $lastError)
            $lastError = json_last_error();

        switch($lastError)
        {
            case JSON_ERROR_NONE:
                $errorString = 'No error has occurred';
                $errorName = 'JSON_ERROR_NONE';
                break;
            case JSON_ERROR_DEPTH:
                $errorString = 'The maximum stack depth has been exceeded';
                $errorName = 'JSON_ERROR_DEPTH';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $errorString = 'Invalid or malformed JSON';
                $errorName = 'JSON_ERROR_STATE_MISMATCH';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $errorString = 'Control character error, possibly incorrectly encoded';
                $errorName = 'JSON_ERROR_CTRL_CHAR';
                break;
            case JSON_ERROR_SYNTAX:
                $errorString = 'Syntax error';
                $errorName = 'JSON_ERROR_SYNTAX';
                break;
            case JSON_ERROR_UTF8:
                $errorString = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                $errorName = 'JSON_ERROR_UTF8';
                break;
            case JSON_ERROR_RECURSION:
                $errorString = 'One or more recursive references in the value to be encoded';
                $errorName = 'JSON_ERROR_RECURSION';
                break;
            case JSON_ERROR_INF_OR_NAN:
                $errorString = 'One or more NAN or INF values in the value to be encoded';
                $errorName = 'JSON_ERROR_INF_OR_NAN';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $errorString = 'A value of a type that cannot be encoded was given';
                $errorName = 'JSON_ERROR_UNSUPPORTED_TYPE';
                break;

            case 42:
                $errorString = 'You have found the answer.';
                $errorName = 'Life, the universe, and everything';
                break;

            default:
                $errorString = 'Unknown error';
                $errorName = 'UNKNOWN';
        }

        if ($includeErrorName)
            return sprintf('%s - %s', $errorName, $errorString);

        return $errorString;
    }
}