<?php
/**
 * Tests for net::stubbles::ipo.response::stubCookie.
 *
 * @package     stubbles
 * @subpackage  ipo_response_test
 * @version     $Id: stubCookieTestCase.php 2888 2011-01-11 22:26:49Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo.response::stubCookie');
/**
 * Tests for net::stubbles::ipo.response::stubCookie.
 *
 * @package     stubbles
 * @subpackage  ipo_response_test
 * @group       ipo
 * @group       ipo_response
 */
class stubCookieTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function returnsGivenName()
    {
        $this->assertEquals('foo', stubCookie::create('foo', 'bar')->getName());
    }

    /**
     * @test
     */
    public function returnsGivenValue()
    {
        $this->assertEquals('bar', stubCookie::create('foo', 'bar')->getValue());
    }

    /**
     * @test
     */
    public function hasNoExpirationDateByDefault()
    {
        $this->assertEquals(0, stubCookie::create('foo', 'bar')->getExpiration());
    }

    /**
     * @test
     */
    public function hasNoPathByDefault()
    {
        $this->assertNull(stubCookie::create('foo', 'bar')->getPath());
    }

    /**
     * @test
     */
    public function hasNoDomainByDefault()
    {
        $this->assertNull(stubCookie::create('foo', 'bar')->getDomain());
    }

    /**
     * @test
     */
    public function isNotSecureByDefault()
    {
        $this->assertFalse(stubCookie::create('foo', 'bar')->isSecure());
    }

    /**
     * @test
     */
    public function isNotHttpOnlyByDefault()
    {
        $this->assertFalse(stubCookie::create('foo', 'bar')->isHttpOnly());
    }

    /**
     * @test
     */
    public function expiresAtUsesGivenTimestamp()
    {
        $expires = time() + 100; // expire after 100 seconds
        $this->assertEquals($expires,
                            stubCookie::create('foo', 'bar')
                                      ->expiringAt($expires)
                                      ->getExpiration()
        );
    }

    /**
     * @test
     * @group  bug255
     */
    public function expiresInAddsCurrentTime()
    {
        $this->assertGreaterThanOrEqual(time() + 100,
                                        stubCookie::create('foo', 'bar')
                                                  ->expiringIn(100)
                                                  ->getExpiration()
        );
    }

    /**
     * @test
     */
    public function usesGivenPath()
    {
        $this->assertEquals('bar',
                            stubCookie::create('foo', 'bar')
                                      ->forPath('bar')
                                      ->getPath()
        );
    }

    /**
     * @test
     */
    public function usesGivenDomain()
    {
        $this->assertEquals('.example.org',
                            stubCookie::create('foo', 'bar')
                                     ->forDomain('.example.org')
                                     ->getDomain()
        );
    }

    /**
     * @test
     */
    public function isSecureIfEnabled()
    {
        $this->assertTrue(stubCookie::create('foo', 'bar')
                                    ->withSecurity(true)
                                    ->isSecure()

         );
    }

    /**
     * @test
     */
    public function isHttpOnlyIfEnabled()
    {
        $this->assertTrue(stubCookie::create('foo', 'bar')
                                    ->usingHttpOnly(true)
                                    ->isHttpOnly()

         );
    }
}
?>