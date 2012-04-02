<?php
/**
 * Tests for net::stubbles::ipo::request::stubModifiableWebRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubModifiableWebRequestTestCase.php 2678 2010-08-23 21:03:57Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubModifiableWebRequest');
/**
 * Helper class for the tests.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 */
class TeststubModifiableWebRequest extends stubModifiableWebRequest
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
 * Tests for net::stubbles::ipo::request::stubModifiableWebRequest.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @group       ipo
 * @group       ipo_request
 */
class stubModifiableWebRequestTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubModifiableWebRequest
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
        $this->request = new TeststubModifiableWebRequest($this->getMock('stubFilterFactory'));
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
     * @test
     */
    public function setParamsOverwritesExistingParam()
    {
        $this->assertSame($this->request, $this->request->setParam('baz', 'bar'));
        $this->assertEquals(array('foo' => 'baz',
                                  'baz' => 'bar',
                                  'bar' => 'foo'
                            ),
                            $this->request->getUnsecureParams()
        );
    }

    /**
     * @test
     */
    public function setParamsAddsNewParams()
    {
        $this->assertSame($this->request, $this->request->setParam('other', 'bar'));
        $this->assertEquals(array('foo'   => 'baz',
                                  'baz' => array('one', 'two"'),
                                  'bar'   => 'foo',
                                  'other' => 'bar'
                            ),
                            $this->request->getUnsecureParams()
        );
    }

    /**
     * @test
     */
    public function removeParams()
    {
        $this->assertSame($this->request, $this->request->removeParam('baz'));
        $this->assertEquals(array('foo' => 'baz',
                                  'bar' => 'foo'
                            ),
                            $this->request->getUnsecureParams()
        );
    }

    /**
     * @test
     */
    public function removeNonExistingParams()
    {
        $this->assertSame($this->request, $this->request->removeParam('other'));
        $this->assertEquals(array('foo' => 'baz',
                                  'baz' => array('one', 'two"'),
                                  'bar' => 'foo'
                            ),
                            $this->request->getUnsecureParams()
        );
    }

    /**
     * @test
     */
    public function setHeaderOverWritesExistingHeader()
    {
        $this->assertSame($this->request, $this->request->setHeader('key', 'otherValue'));
        $this->assertEquals(array('key' => 'otherValue'), $this->request->getUnsecureHeaders());
    }

    /**
     * @test
     */
    public function setHeaderAddsNewHeader()
    {
        $this->assertSame($this->request, $this->request->setHeader('otherkey', 'otherValue'));
        $this->assertEquals(array('key'      => 'value',
                                  'otherkey' => 'otherValue'
                            ),
                            $this->request->getUnsecureHeaders()
        );
    }

    /**
     * @test
     */
    public function removeHeader()
    {
        $this->assertSame($this->request, $this->request->removeHeader('key'));
        $this->assertEquals(array(), $this->request->getUnsecureHeaders()
        );
    }

    /**
     * @test
     */
    public function removeNonExistingHeader()
    {
        $this->assertSame($this->request, $this->request->removeHeader('other'));
        $this->assertEquals(array('key' => 'value'), $this->request->getUnsecureHeaders());
    }

    /**
     * @test
     */
    public function setCookieOverwritesExistingCookie()
    {
        $this->assertSame($this->request, $this->request->setCookie('name', 'otherValue'));
        $this->assertEquals(array('name' => 'otherValue'), $this->request->getUnsecureCookies());
    }

    /**
     * @test
     */
    public function setCookieAddsNewCookie()
    {
        $this->assertSame($this->request, $this->request->setCookie('other', 'otherValue'));
        $this->assertEquals(array('name'  => 'value',
                                  'other' => 'otherValue'
                            ),
                            $this->request->getUnsecureCookies()
        );
    }

    /**
     * @test
     */
    public function removeCookie()
    {
        $this->assertSame($this->request, $this->request->removeCookie('name'));
        $this->assertEquals(array(), $this->request->getUnsecureCookies());
    }

    /**
     * @test
     */
    public function removeNonExistingCookie()
    {
        $this->assertSame($this->request, $this->request->removeCookie('other'));
        $this->assertEquals(array('name' => 'value'), $this->request->getUnsecureCookies());
    }
}
?>