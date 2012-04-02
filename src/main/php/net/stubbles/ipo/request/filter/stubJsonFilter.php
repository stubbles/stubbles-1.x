<?php
/**
 * Class for decoding JSON with not very sophisticated syntax check.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubJsonFilter.php 2257 2009-06-24 10:11:48Z richi $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Class for decoding JSON with not very sophisticated syntax check.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @link        http://www.json.org/
 * @link        http://www.ietf.org/rfc/rfc4627.txt
 */
class stubJsonFilter extends stubBaseObject implements stubFilter
{
    /**
     * Checks if given string is valid JSON.
     *
     * @param   mixed  $value  JSON to filter
     * @return  stdClass|array
     * @throws  stubIllegalArgumentException
     */
    public function execute($value)
    {
        if ($value === null) {
            throw new stubIllegalArgumentException('No proper JSON structure given.');
        }

        if (strlen($value) > 20000) {
            throw new stubIllegalArgumentException('JSON-Input too big - aborted.');
        }

        // JSON can only be an object or an array structure (see JSON spec & RFC)
        if ($value[0] === '{' && $value[strlen($value)-1] !== '}') {
            throw new stubIllegalArgumentException('No proper JSON array/object given.');
        } elseif ($value[0] === '[' && $value[strlen($value)-1] !== ']') {
            throw new stubIllegalArgumentException('No proper JSON array/object given.');
        }

        $decodedJson = json_decode($value);
        if ( (function_exists('json_last_error')
             && json_last_error() !== JSON_ERROR_NONE)
             || $decodedJson === null
             || is_scalar($decodedJson) === true) {

            // JSON can only be an object or an array structure (see JSON spec & RFC)
            // json_decode from php lacks this restriction
            throw new stubIllegalArgumentException('No proper JSON structure given.');
        }

        return $decodedJson;
    }
}
?>