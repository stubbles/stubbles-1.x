<?php
/**
 * Test for net::stubbles::peer::stubURL.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @version     $Id: stubURLTestCase.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubURL');
/**
 * Test for net::stubbles::peer::stubURL.
 *
 * @package     stubbles
 * @subpackage  peer_test
 * @group       peer
 */
class stubURLTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function value()
    {
        $url = stubURL::fromString('http://stubbles.net/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://stubbles.net/', $url->get());
        $this->assertEquals('http://stubbles.net/', $url->get(true));
        $this->assertEquals('http://stubbles.net/', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('stubbles.net', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $url->setPort(303);
        $this->assertEquals(303, $url->getPort());
        $this->assertEquals(303, $url->getPort(313));
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueComplete()
    {
        $url = stubURL::fromString('http://stUBblES.net:80/index.php?content=features#top');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://stubbles.net/index.php?content=features#top', $url->get());
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top', $url->get(true));
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('stubbles.net', $url->getHost());
        $this->assertEquals($url->getPort(), 80);
        $this->assertEquals($url->getPath(), '/index.php?content=features');
        $this->assertTrue($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueFTPComplete()
    {
        $url = stubURL::fromString('ftp://user:password@stubbles.net/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('ftp://user:password@stubbles.net/', $url->get());
        $this->assertEquals('ftp://user:password@stubbles.net/', $url->get(true));
        $this->assertEquals('ftp://user:password@stubbles.net/', $url->get(true, true));
        $this->assertEquals('ftp', $url->getScheme());
        $this->assertEquals('user', $url->getUser());
        $this->assertEquals('user', $url->getUser('foo'));
        $this->assertEquals('password', $url->getPassword());
        $this->assertEquals('password', $url->getPassword('foo'));
        $this->assertEquals('stubbles.net', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueFTPWithoutPass()
    {
        $url = stubURL::fromString('ftp://user@stubbles.net/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('ftp://user@stubbles.net/', $url->get());
        $this->assertEquals('ftp://user@stubbles.net/', $url->get(true));
        $this->assertEquals('ftp://user@stubbles.net/', $url->get(true, true));
        $this->assertEquals('ftp', $url->getScheme());
        $this->assertEquals('user', $url->getUser());
        $this->assertEquals('user', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('stubbles.net', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueFTPEmptyPass()
    {
        $url = stubURL::fromString('ftp://user:@stubbles.net/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('ftp://user:@stubbles.net/', $url->get());
        $this->assertEquals('ftp://user:@stubbles.net/', $url->get(true));
        $this->assertEquals('ftp://user:@stubbles.net/', $url->get(true, true));
        $this->assertEquals('ftp', $url->getScheme());
        $this->assertEquals('user', $url->getUser());
        $this->assertEquals('user', $url->getUser('foo'));
        $this->assertEquals('', $url->getPassword());
        $this->assertEquals('', $url->getPassword('foo'));
        $this->assertEquals('stubbles.net', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueFTPEmptyUser()
    {
        $url = stubURL::fromString('ftp://@stubbles.net/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('ftp://@stubbles.net/', $url->get());
        $this->assertEquals('ftp://@stubbles.net/', $url->get(true));
        $this->assertEquals('ftp://@stubbles.net/', $url->get(true, true));
        $this->assertEquals('ftp', $url->getScheme());
        $this->assertEquals('', $url->getUser());
        $this->assertEquals('', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('stubbles.net', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueLocalhost()
    {
        $url = stubURL::fromString('http://localhost/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://localhost/', $url->get());
        $this->assertEquals('http://localhost/', $url->get(true));
        $this->assertEquals('http://localhost/', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('localhost', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueIP()
    {
        $url = stubURL::fromString('http://127.0.0.1/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://127.0.0.1/', $url->get());
        $this->assertEquals('http://127.0.0.1/', $url->get(true));
        $this->assertEquals('http://127.0.0.1/', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('127.0.0.1', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueIPNoPath()
    {
        $url = stubURL::fromString('http://127.0.0.1');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://127.0.0.1', $url->get());
        $this->assertEquals('http://127.0.0.1', $url->get(true));
        $this->assertEquals('http://127.0.0.1', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('127.0.0.1', $url->getHost());
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertNull($url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertTrue($url->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function fileURL()
    {
        $url = stubURL::fromString('file:///home');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('file:///home', $url->get());
        $this->assertEquals('file:///home', $url->get(true));
        $this->assertEquals('file:///home', $url->get(true, true));
        $this->assertEquals('file', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertNull($url->getHost());
        $this->assertEquals('127.0.0.1', $url->getHost('127.0.0.1'));
        $this->assertNull($url->getPort());
        $this->assertEquals(313, $url->getPort(313));
        $this->assertEquals('/home', $url->getPath());
        $this->assertFalse($url->hasQuery());
        $this->assertFalse($url->checkDNS());
    }

    /**
     * assure that wrong values trigger an exception
     *
     * @test
     * @expectedException  stubMalformedURLException
     */
    public function wrongValue()
    {
        $url = stubURL::fromString('blubber');
    }

    /**
     * assure that an empty string does not generate an instance of stubURL
     *
     * @test
     */
    public function emptyString()
    {
        $this->assertNull(stubURL::fromString(''));
    }

    /**
     * assure that added parameters are correct in complete url and path
     *
     * @test
     */
    public function params()
    {
        $url = stubURL::fromString('http://example.org/');
        $this->assertFalse($url->hasQuery());
        $this->assertFalse($url->hasParam('test'));

        $this->assertSame($url, $url->addParam('test', 'hello'));
        $this->assertTrue($url->hasQuery());
        $this->assertTrue($url->hasParam('test'));
        $this->assertEquals('http://example.org/?test=hello', $url->get());
        $this->assertEquals('http://example.org/?test=hello', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello', $url->get(true, true));
        $this->assertEquals('/?test=hello', $url->getPath());

        $this->assertSame($url, $url->addParam('test2', 538));
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?test=hello&test2=538', $url->get());
        $this->assertEquals('http://example.org/?test=hello&test2=538', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello&test2=538', $url->get(true, true));
        $this->assertEquals('/?test=hello&test2=538', $url->getPath());

        $this->assertSame($url, $url->addParam('test3', array(1, 2, 3)));
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[]=1&test3[]=2&test3[]=3', $url->get());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[]=1&test3[]=2&test3[]=3', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[]=1&test3[]=2&test3[]=3', $url->get(true, true));
        $this->assertEquals('/?test=hello&test2=538&test3[]=1&test3[]=2&test3[]=3', $url->getPath());

        $this->assertSame($url, $url->addParam('test3', array('one' => 1, 'two' => 2, 'three' => 3)));
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[three]=3', $url->get());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[three]=3', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[three]=3', $url->get(true, true));
        $this->assertEquals('/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[three]=3', $url->getPath());

        $this->assertSame($url, $url->addParam('test3', array('one' => 1, 'two' => 2, 3)));
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[]=3', $url->get());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[]=3', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[]=3', $url->get(true, true));
        $this->assertEquals('/?test=hello&test2=538&test3[one]=1&test3[two]=2&test3[]=3', $url->getPath());

        $this->assertSame($url, $url->addParam('test3', null));
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?test=hello&test2=538', $url->get());
        $this->assertEquals('http://example.org/?test=hello&test2=538', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello&test2=538', $url->get(true, true));
        $this->assertEquals('/?test=hello&test2=538', $url->getPath());

        $this->assertSame($url, $url->addParam('test3', true));
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3=1', $url->get());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3=1', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3=1', $url->get(true, true));
        $this->assertEquals('/?test=hello&test2=538&test3=1', $url->getPath());

        $this->assertSame($url, $url->addParam('test3', false));
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3=0', $url->get());
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3=0', $url->get(true));
        $this->assertEquals('http://example.org/?test=hello&test2=538&test3=0', $url->get(true, true));
        $this->assertEquals('/?test=hello&test2=538&test3=0', $url->getPath());
    }

    /**
     * assure that paramter without value is valid
     * e.g.: http://example.org?wsdl
     *       http://example.org?key1&foo=bar&key2
     *
     * @test
     */
    public function keyWithoutParam()
    {
        $url = stubURL::fromString('http://example.org/');
        $this->assertFalse($url->hasQuery());
        $this->assertSame($url, $url->addParam('key1', ''));
        $this->assertTrue($url->hasQuery());
        $this->assertTrue($url->hasParam('key1'));
        $this->assertEquals('http://example.org/?key1', $url->get());
        $this->assertEquals('http://example.org/?key1', $url->get(true));
        $this->assertEquals('http://example.org/?key1', $url->get(true, false));
        $this->assertSame($url, $url->addParam('foo', 'bar'));
        $this->assertSame($url, $url->addParam('key2', ''));
        $this->assertTrue($url->hasParam('foo'));
        $this->assertTrue($url->hasParam('key2'));
        $this->assertEquals('http://example.org/?key1&foo=bar&key2', $url->get());
        $this->assertEquals('http://example.org/?key1&foo=bar&key2', $url->get(true));
        $this->assertEquals('http://example.org/?key1&foo=bar&key2', $url->get(true, true));
    }

    /**
     * @test
     */
    public function paramsWithDots()
    {
        $url = stubURL::fromString('http://example.org/?foo.bar=baz.baz');
        $this->assertTrue($url->hasQuery());
        $this->assertEquals('http://example.org/?foo.bar=baz.baz', $url->get());
        $this->assertEquals('http://example.org/?foo.bar=baz.baz', $url->get(true));
        $this->assertEquals('http://example.org/?foo.bar=baz.baz', $url->get(true, false));
        $this->assertSame($url, $url->addParam('bar.baz', 'foo.foo'));
        $this->assertEquals('http://example.org/?foo.bar=baz.baz&bar.baz=foo.foo', $url->get());
        $this->assertEquals('http://example.org/?foo.bar=baz.baz&bar.baz=foo.foo', $url->get(true));
        $this->assertEquals('http://example.org/?foo.bar=baz.baz&bar.baz=foo.foo', $url->get(true, false));
    }

    /**
     * @test
     * @since  1.1.2
     */
    public function removeParam()
    {
        $url = stubURL::fromString('http://example.org/?foo.bar=baz.baz&bar.baz=foo.foo');
        $this->assertTrue($url->hasQuery());
        $this->assertTrue($url->hasParam('foo.bar'));
        $this->assertEquals('http://example.org/?foo.bar=baz.baz&bar.baz=foo.foo', $url->get());
        $this->assertEquals('http://example.org/?foo.bar=baz.baz&bar.baz=foo.foo', $url->get(true));
        $this->assertEquals('http://example.org/?foo.bar=baz.baz&bar.baz=foo.foo', $url->get(true, false));
        $this->assertSame($url, $url->removeParam('foo.bar'));
        $this->assertFalse($url->hasParam('foo.bar'));
        $this->assertEquals('http://example.org/?bar.baz=foo.foo', $url->get());
        $this->assertEquals('http://example.org/?bar.baz=foo.foo', $url->get(true));
        $this->assertEquals('http://example.org/?bar.baz=foo.foo', $url->get(true, false));
    }

    /**
     * @test
     * @since  1.1.2
     */
    public function removeLastParam()
    {
        $url = stubURL::fromString('http://example.org/?foo.bar=baz.baz');
        $this->assertTrue($url->hasQuery());
        $this->assertTrue($url->hasParam('foo.bar'));
        $this->assertEquals('http://example.org/?foo.bar=baz.baz', $url->get());
        $this->assertEquals('http://example.org/?foo.bar=baz.baz', $url->get(true));
        $this->assertEquals('http://example.org/?foo.bar=baz.baz', $url->get(true, false));
        $this->assertSame($url, $url->removeParam('foo.bar'));
        $this->assertFalse($url->hasParam('foo.bar'));
        $this->assertEquals('http://example.org/', $url->get());
        $this->assertEquals('http://example.org/', $url->get(true));
        $this->assertEquals('http://example.org/', $url->get(true, false));
    }

    /**
     * assure that wrong parameters throw an exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function wrongParams()
    {
        $url = stubURL::fromString('http://example.org/');
        $url->addParam('test', new stdClass());
    }

    /**
     * assure that wrong keys throw an exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function wrongKeyForParams()
    {
        $url = stubURL::fromString('http://example.org/');
        $url->addParam(435, 'test');
    }

    /**
     * assure sheme can be replaced before build URL
     *
     * @test
     */
    public function changeShemeFromHttpToHttpsAndBack()
    {
        $url = stubURL::fromString('http://example.org/');
        $url->setScheme('https');
        $this->assertEquals('https://example.org/', $url->get());

        $url->setScheme('http');
        $this->assertEquals('http://example.org/', $url->get());
    }

    /**
     * @test
     */
    public function paramWithoutValue()
    {
        $url = stubURL::fromString('http://example.org/?wsdl');
        $this->assertEquals('http://example.org/?wsdl', $url->get());
    }

    /**
     * @test
     * @group  bug258
     */
    public function ipv6AddressShortNotation()
    {
        $url = stubURL::fromString('http://[2001:db8:12:34::1]:80/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://[2001:db8:12:34::1]/', $url->get());
        $this->assertEquals('http://[2001:db8:12:34::1]:80/', $url->get(true));
        $this->assertEquals('http://[2001:db8:12:34::1]:80/', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('[2001:db8:12:34::1]', $url->getHost());
        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        # can not reliably detect whether machine supports IPv6
        #$this->assertTrue($url->checkDNS());
    }

    /**
     * @test
     * @group  bug258
     */
    public function ipv6AddressComplete()
    {
        $url = stubURL::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/foo');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]/foo', $url->get());
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/foo', $url->get(true));
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:80/foo', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('[2001:8d8f:1fe:5:abba:dbff:fefe:7755]', $url->getHost());
        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('/foo', $url->getPath());
        $this->assertFalse($url->hasQuery());
        # can not reliably detect whether machine supports IPv6
        #$this->assertTrue($url->checkDNS());
    }

    /**
     * @test
     * @group  bug258
     */
    public function ipv6AddressLocalhost()
    {
        $url = stubURL::fromString('http://[::1]:80/');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://[::1]/', $url->get());
        $this->assertEquals('http://[::1]:80/', $url->get(true));
        $this->assertEquals('http://[::1]:80/', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('[::1]', $url->getHost());
        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('/', $url->getPath());
        $this->assertFalse($url->hasQuery());
        # special case localhost is short-cicuited in url class
        $this->assertTrue($url->checkDNS());
    }
}
?>