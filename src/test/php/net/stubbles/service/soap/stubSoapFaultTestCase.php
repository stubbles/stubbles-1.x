<?php
/**
 * Test for net::stubbles::service::soap::stubSoapFault.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 * @version     $Id: stubSoapFaultTestCase.php 2142 2009-03-27 13:47:27Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::stubSoapFault');
/**
 * Tests for net::stubbles::service::soap::stubSoapFault.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 * @group       service_soap
 */
class stubSoapFaultTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a simple fault requires only a fault code and a fault string
     *
     * @test
     */
    public function simpleFault()
    {
        $soapFault = new stubSoapFault('code', 'string');
        $this->assertEquals('code', $soapFault->getFaultCode());
        $this->assertEquals('string', $soapFault->getFaultString());
        $this->assertNull($soapFault->getFaultActor());
        $this->assertNull($soapFault->getDetail());
    }

    /**
     * a fault with all informations
     *
     * @test
     */
    public function fullFault()
    {
        $soapFault = new stubSoapFault('code', 'string', 'actor', 'detail');
        $this->assertEquals('code', $soapFault->getFaultCode());
        $this->assertEquals('string', $soapFault->getFaultString());
        $this->assertEquals('actor', $soapFault->getFaultActor());
        $this->assertEquals('detail', $soapFault->getDetail());
    }
}
?>