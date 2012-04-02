<?php
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @version     $Id: stubXslCallbackTestCase.php 2964 2011-02-07 17:56:59Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::callback::stubXslCallback');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 */
class TestXslCallback extends stubBaseObject
{
    /**
     * example method
     *
     * @return  string
     * @XslMethod
     */
    public function hello($world)
    {
        return 'hello ' . $world;
    }
    
    /**
     * example method
     *
     * @return  string
     */
    public function youCanNotCallMe()
    {
        return 'bye world!';
    }
    
    /**
     * example method
     *
     * @return  string
     * @XslMethod
     */
    protected function doNotCallMe()
    {
        return 'A protected method was called!';
    }
    
    /**
     * example method
     *
     * @return  string
     * @XslMethod
     */
    private function doNotCallMeToo()
    {
        return 'A private method was called.';
    }
    
    /**
     * example method
     *
     * @return  string
     * @XslMethod
     */
    public static function youCanDoThis()
    {
        return 'A static method was called.';
    }
}
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @group       xml
 * @group       xml_xsl
 * @group       xml_xsl_callback
 */
class stubXslCallbackTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * callback class used for tests
     *
     * @var  TestXslCallback
     */
    protected $callback;
    /**
     * instance to test
     *
     * @var  stubXslCallback
     */
    protected $xslCallback;
    
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->callback    = new TestXslCallback();
        $this->xslCallback = new stubXslCallback();
        $this->xslCallback->setCallback('test', $this->callback);
    }

    /**
     * @test
     */
    public function addedCallbackIsAvailable()
    {
        $this->assertTrue($this->xslCallback->hasCallback('test'));
        $this->assertEquals($this->callback, $this->xslCallback->getCallback('test'));
    }

    /**
     * @test
     * @expectedException  stubXslCallbackException
     */
    public function callbackDoesNotExistThrowsCallbackException()
    {
        $this->xslCallback->invoke('foo', 'hello');
    }

    /**
     * @test
     * @expectedException  stubXslCallbackException
     */
    public function callbackMethodNotAnnotatedThrowsCallbackException()
    {
       $this->xslCallback->invoke('test', 'youCanNotCallMe');
    }

    /**
     * @test
     * @expectedException  stubXslCallbackException
     */
    public function callingProtectedCallbackMethodThrowsCallbackException()
    {
        $this->xslCallback->invoke('test', 'doNotCallMe');
    }

    /**
     * @test
     * @expectedException  stubXslCallbackException
     */
    public function callingPrivateCallbackMethodThrowsCallbackException()
    {
        $this->xslCallback->invoke('test', 'doNotCallMeToo');
    }

    /**
     * @test
     */
    public function invokeReturnsValueFromCallbackMethod()
    {
        $this->assertEquals('hello world!',
                            $this->xslCallback->invoke('test', 'hello', array('world!'))
        );
    }

    /**
     * @test
     */
    public function invokeReturnsValueFromStaticCallbackMethod()
    {
        $this->assertEquals('A static method was called.',
                            $this->xslCallback->invoke('test', 'youCanDoThis')
        );
    }
}
?>