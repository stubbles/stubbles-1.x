<?php
/**
 * Serializer for response instances.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 * @version     $Id: stubResponseSerializer.php 3304 2012-01-04 10:04:05Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Serializer for response instances.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 * @since       1.7.0
 * @Singleton
 */
class stubResponseSerializer extends stubBaseObject
{
    /**
     * unserializes string into a response
     *
     * @param   string
     * @return  stubResponse
     */
    public function unserialize($serialized)
    {
        $response = @unserialize($serialized);
        if ($response instanceof stubResponse) {
            return $response;
        }

        throw new stubIllegalArgumentException('Invalid serialized response.');
    }

    /**
     * serializes response into a string
     *
     * @param   stubResponse
     * @return  string
     */
    public function serialize(stubResponse $response)
    {
        return serialize($response);
    }

    /**
     * serialize response without cookies
     *
     * @param   stubResponse  $response
     * @return  string
     */
    public function serializeWithoutCookies(stubResponse $response)
    {
        $class = stubClassLoader::getNonQualifiedClassName($response->getClassName());
        /* @var $other stubResponse */
        $other = new $class($response->getVersion());
        $other->write($response->getBody());
        foreach ($response->getHeaders() as $name => $value) {
            $other->addHeader($name, $value);
        }

        return serialize($other);
    }
}
?>