<?php
/**
 * Utility class for creates json data.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc
 * @version     $Id: stubJsonRpcWriter.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubRecursiveStringEncoder',
                      'net::stubbles::php::string::stubUTF8Encoder'
);
/**
 * Utility class for creates json data.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc
 * @link        http://json-rpc.org/wiki/specification
 * @static
 */
class stubJsonRpcWriter extends stubBaseObject
{
    /**
     * encoder instancer
     *
     * @var  stubStringEncoder
     */
    protected static $encoder;

    /**
     * static initializing
     */
    // @codeCoverageIgnoreStart
    public static function __static()
    {
        self::$encoder = new stubRecursiveStringEncoder(new stubUTF8Encoder());
    }
    // @codeCoverageIgnoreEnd

    /**
     * send a json fault
     *
     * @param   string  $reqId    request id
     * @param   string  $message  fault message
     * @return  string
     */
    public static function writeFault($reqId, $message)
    {
        $fault = array('id'     => $reqId,
                       'result' => null,
                       'error'  => self::$encoder->encode($message)
                 );
        return json_encode($fault);
    }

    /**
     * send a json response
     *
     * @param   string  $reqId    request id
     * @param   string  $result
     * @return  string
     */
    public static function writeResponse($reqId, $result)
    {
        $response = array('id'     => $reqId,
                          'result' => self::$encoder->encode($result),
                          'error'  => null
                    );
        return json_encode($response);
    }
}
?>