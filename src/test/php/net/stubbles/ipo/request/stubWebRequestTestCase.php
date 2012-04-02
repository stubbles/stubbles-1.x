<?php
/**
 * Tests for net::stubbles::ipo::request::stubWebRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubWebRequestTestCase.php 2678 2010-08-23 21:03:57Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubWebRequest');
/**
 * Helper class for the tests.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 */
class TeststubWebRequest extends stubWebRequest
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
 * Tests for net::stubbles::ipo::request::stubWebRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @group       ipo
 * @group       ipo_request
 */
class stubWebRequestTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubWebRequest
     */
    protected $request;
    /**
     * backup copy of original data
     *
     * @var  array
     */
    protected $originalData = array();

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->originalData['server'] = $_SERVER;
        $this->originalData['cookie'] = $_COOKIE;
        $this->originalData['get']    = $_GET;
        $this->originalData['post']   = $_POST;
        $_GET    = array('foo' => 'bar', 'baz' => array('one', 'two\"'));
        $_POST   = array('foo' => 'baz', 'bar' => 'foo');
        $_SERVER = array('key' => 'value');
        $_COOKIE = array('name' => 'value');
        $this->request = new TeststubWebRequest($this->getMock('stubFilterFactory'));
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        $_SERVER = $this->originalData['server'];
        $_COOKIE = $this->originalData['cookie'];
        $_GET    = $this->originalData['get'];
        $_POST   = $this->originalData['post'];
    }

    /**
     * handling of get/post params
     *
     * @test
     */
    public function params()
    {
        $this->assertEquals(array('foo' => 'baz', 'baz' => array('one', 'two"'), 'bar' => 'foo'), $this->request->getUnsecureParams());
    }

    /**
     * handling of header params
     *
     * @test
     */
    public function header()
    {
        $this->assertEquals(array('key' => 'value'), $this->request->getUnsecureHeaders());
    }

    /**
     * handling of header params
     *
     * @test
     */
    public function cookie()
    {
        $this->assertEquals(array('name' => 'value'), $this->request->getUnsecureCookies());
    }

    /**
     * correct request method should be returned
     *
     * @test
     */
    public function getMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('post', $this->request->getMethod());
        $_SERVER['REQUEST_METHOD'] = 'post';
        $this->assertEquals('post', $this->request->getMethod());
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertEquals('get', $this->request->getMethod());
        $_SERVER['REQUEST_METHOD'] = 'get';
        $this->assertEquals('get', $this->request->getMethod());
    }

    /**
     * correct uri should be returned
     *
     * @test
     */
    public function getURI()
    {
        $_SERVER['HTTP_HOST'] = 'example.org';
        $_SERVER['REQUEST_URI'] = '/index.php';
        $this->assertEquals('example.org/index.php', $this->request->getURI());
        unset($_SERVER['HTTP_HOST']);
        $this->assertEquals('/index.php', $this->request->getURI());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getCompleteUriInSsl()
    {
        $_SERVER['SERVER_PORT'] = 443;
        $_SERVER['HTTP_HOST']   = 'example.org';
        $_SERVER['REQUEST_URI'] = '/index.php?foo=bar';
        $this->assertEquals('https://example.org/index.php?foo=bar', $this->request->getCompleteUri());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getCompleteUriInNonSsl()
    {
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['HTTP_HOST']   = 'example.org';
        $_SERVER['REQUEST_URI'] = '/index.php?foo=bar';
        $this->assertEquals('http://example.org/index.php?foo=bar', $this->request->getCompleteUri());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function getCompleteUriWithoutPortSet()
    {
        unset($_SERVER['SERVER_PORT']);
        $_SERVER['HTTP_HOST']   = 'example.org';
        $_SERVER['REQUEST_URI'] = '/index.php?foo=bar';
        $this->assertEquals('http://example.org/index.php?foo=bar', $this->request->getCompleteUri());
    }
}
?>