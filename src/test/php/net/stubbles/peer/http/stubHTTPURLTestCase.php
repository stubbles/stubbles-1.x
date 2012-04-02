<?php
/**
 * Test for net::stubbles::peer::http::stubHTTPURL.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @version     $Id: stubHTTPURLTestCase.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::http::stubHTTPURL');
/**
 * Test for net::stubbles::peer::http::stubHTTPURL.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @group       peer
 * @group       peer_http
 */
class stubHTTPURLTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function value()
    {
        $http = stubHTTPURL::fromString('http://stubbles.net/');
        $this->assertTrue($http->isValid());
        $this->assertTrue($http->hasDefaultPort());
        $this->assertEquals('http://stubbles.net/', $http->get());
        $this->assertEquals('http://stubbles.net:80/', $http->get(true));
        $this->assertEquals('http://stubbles.net/', $http->get(true, true));
        $this->assertEquals('http', $http->getScheme());
        $this->assertEquals('stubbles.net', $http->getHost());
        $this->assertEquals(80, $http->getPort());
        $this->assertEquals('/', $http->getPath());
        $this->assertFalse($http->hasQuery());
        $this->assertTrue($http->checkDNS());
        
        $http = stubHTTPURL::fromString('https://stubbles.net/');
        $this->assertTrue($http->isValid());
        $this->assertTrue($http->hasDefaultPort());
        $this->assertEquals('https://stubbles.net/', $http->get());
        $this->assertEquals('https://stubbles.net:443/', $http->get(true));
        $this->assertEquals('https://stubbles.net/', $http->get(true, true));
        $this->assertEquals('https', $http->getScheme());
        $this->assertEquals('stubbles.net', $http->getHost());
        $this->assertEquals(443, $http->getPort());
        $this->assertEquals('/', $http->getPath());
        $this->assertFalse($http->hasQuery());
        $this->assertTrue($http->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueComplete()
    {    
        $http = stubHTTPURL::fromString('http://stUBBles.net:80/index.php?content=features#top');
        $this->assertTrue($http->isValid());
        $this->assertTrue($http->hasDefaultPort());
        $this->assertEquals('http://stubbles.net/index.php?content=features#top', $http->get());
        $this->assertEquals('http://stubbles.net:80/index.php?content=features#top', $http->get(true));
        $this->assertEquals('http://stubbles.net/index.php?content=features#top', $http->get(true, true));
        $this->assertEquals('stubbles.net', $http->getHost());
        $this->assertEquals(80, $http->getPort());
        $this->assertEquals('/index.php?content=features', $http->getPath());
        $this->assertTrue($http->hasQuery());
        $this->assertTrue($http->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueLocalhost()
    {
        $http = stubHTTPURL::fromString('http://localhost:125/');
        $this->assertTrue($http->isValid());
        $this->assertFalse($http->hasDefaultPort());
        $this->assertEquals('http://localhost/', $http->get());
        $this->assertEquals('http://localhost:125/', $http->get(true));
        $this->assertEquals('http://localhost:125/', $http->get(true, true));
        $this->assertEquals('localhost', $http->getHost());
        $this->assertEquals(125, $http->getPort());
        $this->assertEquals('/', $http->getPath());
        $this->assertFalse($http->hasQuery());
        $this->assertTrue($http->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueIP()
    {
        $http = stubHTTPURL::fromString('http://127.0.0.1/');
        $this->assertTrue($http->isValid());
        $this->assertTrue($http->hasDefaultPort());
        $this->assertEquals('http://127.0.0.1/', $http->get());
        $this->assertEquals('http://127.0.0.1:80/', $http->get(true));
        $this->assertEquals('http://127.0.0.1/', $http->get(true, true));
        $this->assertEquals('127.0.0.1', $http->getHost());
        $this->assertEquals(80, $http->getPort());
        $this->assertEquals($http->getPath(), '/');
        $this->assertFalse($http->hasQuery());
        $this->assertTrue($http->checkDNS());
    }

    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function valueHTTPSIP()
    {
        $http = stubHTTPURL::fromString('https://127.0.0.1:125/');
        $this->assertTrue($http->isValid());
        $this->assertFalse($http->hasDefaultPort());
        $this->assertEquals('https://127.0.0.1/', $http->get());
        $this->assertEquals('https://127.0.0.1:125/', $http->get(true));
        $this->assertEquals('https://127.0.0.1:125/', $http->get(true, true));
        $this->assertEquals('127.0.0.1', $http->getHost());
        $this->assertEquals(125, $http->getPort());
        $this->assertEquals($http->getPath(), '/');
        $this->assertFalse($http->hasQuery());
        $this->assertTrue($http->checkDNS());
    }

    /**
     * assure that wrong values trigger an exception
     *
     * @test
     */
    public function wrongValue()
    {
        $this->setExpectedException('stubMalformedURLException');
        $url = stubHTTPURL::fromString('blubber');
    }

    /**
     * assure that an empty string does not generate an instance of stubURL
     *
     * @test
     */
    public function emptyString()
    {
        $this->assertNull(stubHTTPURL::fromString(''));
    }

    /**
     * assure that wrong scheme triggers an exception
     *
     * @test
     */
    public function wrongScheme()
    {
        $this->setExpectedException('stubMalformedURLException');
        $url = stubHTTPURL::fromString('ftp://user:password@auxiliary.kl-s.com/');
    }

    /**
     * assure getting a connection works as expected
     *
     * @test
     */
    public function connection()
    {
        $http = stubHTTPURL::fromString('http://example.com/');
        $httpconnection = $http->connect();
        $this->assertInstanceOf('stubHTTPConnection', $httpconnection);
    }

    /**
     * assure getting a connection works as expected
     *
     * @test
     */
    public function connectionWithHeaderList()
    {
        $http    = stubHTTPURL::fromString('http://example.com/');
        $headers = new stubHeaderList();
        $httpconnection = $http->connect($headers);
        $this->assertInstanceOf('stubHTTPConnection', $httpconnection);
        $this->assertSame($headers, $httpconnection->getHeaderList());
    }

    /**
     * @test
     * @group  bug258
     */
    public function ipv6AddressShortNotation()
    {
        $url = stubHTTPURL::fromString('http://[2001:db8:12:34::1]/');
        $this->assertTrue($url->isValid());
        $this->assertTrue($url->hasDefaultPort());
        $this->assertEquals('http://[2001:db8:12:34::1]/', $url->get());
        $this->assertEquals('http://[2001:db8:12:34::1]:80/', $url->get(true));
        $this->assertEquals('http://[2001:db8:12:34::1]/', $url->get(true, true));
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
        $url = stubHTTPURL::fromString('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:8080/foo');
        $this->assertTrue($url->isValid());
        $this->assertFalse($url->hasDefaultPort());
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]/foo', $url->get());
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:8080/foo', $url->get(true));
        $this->assertEquals('http://[2001:8d8f:1fe:5:abba:dbff:fefe:7755]:8080/foo', $url->get(true, true));
        $this->assertEquals('http', $url->getScheme());
        $this->assertNull($url->getUser());
        $this->assertEquals('foo', $url->getUser('foo'));
        $this->assertNull($url->getPassword());
        $this->assertEquals('foo', $url->getPassword('foo'));
        $this->assertEquals('[2001:8d8f:1fe:5:abba:dbff:fefe:7755]', $url->getHost());
        $this->assertEquals(8080, $url->getPort());
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
        $url = stubHTTPURL::fromString('http://[::1]:80/');
        $this->assertTrue($url->isValid());
        $this->assertTrue($url->hasDefaultPort());
        $this->assertEquals('http://[::1]/', $url->get());
        $this->assertEquals('http://[::1]:80/', $url->get(true));
        $this->assertEquals('http://[::1]/', $url->get(true, true));
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