<?php
/**
 * Test for net::stubbles::ipo::request::stubRedirectRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubRedirectRequestTestCase.php 2679 2010-08-23 21:26:55Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRedirectRequest');
/**
 * Helper class for the tests.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 */
class TestRedirectRequest extends stubRedirectRequest
{
    /**
     * direct access to unsecure params data
     *
     * @return  array<string,string>
     */
    public function getUnsecureParams()
    {
        return $this->unsecureParams;
    }

    /**
     * direct access to unsecure headers data
     *
     * @return  array<string,string>
     */
    public function getUnsecureHeaders()
    {
        return $this->unsecureHeaders;
    }

    /**
     * direct access to unsecure cookies data
     *
     * @return  array<string,string>
     */
    public function getUnsecureCookies()
    {
        return $this->unsecureCookies;
    }
}
/**
 * Test for net::stubbles::ipo::request::stubRedirectRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @group       ipo
 * @group       ipo_request
 */
class stubRedirectRequestTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * on a non redirected query the get params should be used
     *
     * @test
     */
    public function nonRedirectedQuery()
    {
        $_GET    = array('foo' => 'bar', 'baz' => array('one', 'two'));
        $_POST   = array('foo' => 'baz', 'bar' => 'foo');
        $_SERVER = array('key' => 'value');
        $_COOKIE = array('name' => 'value');
        $request = new TestRedirectRequest($this->getMock('stubFilterFactory'));
        $this->assertEquals(array('foo' => 'bar', 'baz' => array('one', 'two')), $request->getUnsecureParams());
        $this->assertEquals(array('key' => 'value'), $request->getUnsecureHeaders());
        $this->assertEquals(array('name' => 'value'), $request->getUnsecureCookies());
    }

    /**
     * on a redirected query the params should be extracted from the redirect query string
     *
     * @test
     */
    public function redirectedQuery()
    {
        $_GET    = array('foo' => 'bar', 'baz' => array('one', 'two'));
        $_POST   = array('foo' => 'baz', 'bar' => 'foo');
        $_SERVER = array('key' => 'value', 'REDIRECT_QUERY_STRING' => 'foo[]=bar&foo[]=baz&bar=onetwo');
        $_COOKIE = array('name' => 'value');
        $request = new TestRedirectRequest($this->getMock('stubFilterFactory'));
        $this->assertEquals(array('foo' => array('bar', 'baz'), 'bar' => 'onetwo'), $request->getUnsecureParams());
        $this->assertEquals(array('key' => 'value', 'REDIRECT_QUERY_STRING' => 'foo[]=bar&foo[]=baz&bar=onetwo'), $request->getUnsecureHeaders());
        $this->assertEquals(array('name' => 'value'), $request->getUnsecureCookies());
    }
}
?>