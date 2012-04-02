<?php
/**
 * Test for net::stubbles::service::rest::stubRestHandlerException.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id: stubRestHandlerExceptionTestCase.php 3202 2011-10-26 14:46:50Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::stubRestHandlerException');
/**
 * Test for net::stubbles::service::rest::stubRestHandlerException.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @group       service
 * @group       service_rest
 */
class stubRestHandlerExceptionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function withStringAsCause()
    {
        $rhe = new stubRestHandlerException(400, 'Bad Request', 'error message');
        $this->assertEquals(400, $rhe->getStatusCode());
        $this->assertEquals('Bad Request', $rhe->getStatusMessage());
        $this->assertEquals('error message', $rhe->getMessage());
        $this->assertNull($rhe->getCause());
    }

    /**
     * @test
     */
    public function withExceptionAsCause()
    {
        $cause = new Exception('error message');
        $rhe   = new stubRestHandlerException(400, 'Bad Request', $cause);
        $this->assertEquals(400, $rhe->getStatusCode());
        $this->assertEquals('Bad Request', $rhe->getStatusMessage());
        $this->assertEquals('error message', $rhe->getMessage());
        $this->assertSame($cause, $rhe->getCause());
    }
}
?>