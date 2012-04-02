<?php
/**
 * Tests for net::stubbles::peer::http::stubLDAPURL.
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @version     $Id: stubLDAPURLTestCase.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::ldap::stubLDAPURL');
/**
 * Tests for net::stubbles::peer::http::stubLDAPURL.
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @group       peer
 * @group       peer_ldap
 */
class stubLDAPURLTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('ldap') === false) {
            $this->markTestSkipped('The LDAP extension is not available.');
        }
    }

    /**
     * assure url roundtrip without port
     *
     * @test
     */
    public function urlWithoutPort()
    {
        $url = 'ldap://ldap.example.com/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get());

        $expected = 'ldap://ldap.example.com:389/dc=example,dc=com';
        $this->assertEquals($expected, $ldap->get(true));
    }

    /**
     * assure url roundtrip with standard port
     *
     * @test
     */
    public function urlWithPort()
    {
        $url = 'ldap://ldap.example.com:389/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));
    }

    /**
     * assure url roundtrip with arbitrary port
     *
     * @test
     */
    public function urlWithArbitraryPort()
    {
        $url = 'ldap://ldap.example.com:123/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));
    }

    /**
     * assure url roundtrip with host ip
     *
     * @test
     */
    public function urlWithHostIP()
    {
        $url = 'ldap://127.0.0.1:389/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));
    }

    /**
     * assure url roundtrip with localhost
     *
     * @test
     */
    public function urlWithLocalhost()
    {
        $url = 'ldap://localhost:389/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));
    }

    /**
     * assure url roundtrip without host and port
     *
     * @test
     */
    public function urlWithoutHostAndPort()
    {
        $url      = 'ldap:///dc=example,dc=com';
        $expected = 'ldap://localhost:389/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));
    }

    /**
     * assure url roundtrip without authentication data
     *
     * @test
     */
    public function urlWithAuth()
    {
        $url = 'ldap://user:password@ldap.example.com:389/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));
    }

    /**
     * assure url roundtrip with params (all possibilitiess)
     *
     * @test
     */
    public function urlWithParams()
    {
        $url      = 'ldap://localhost:389/dc=example,dc=com?cn';
        $expected = 'ldap://localhost:389/dc=example,dc=com?cn?base?(objectClass=*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));

        $url      = 'ldap://localhost:389/dc=example,dc=com?cn?sub';
        $expected = 'ldap://localhost:389/dc=example,dc=com?cn?sub?(objectClass=*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));

        $url  = 'ldap://localhost:389/dc=example,dc=com?cn?sub?(cn=John Doe)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));

        $url  = 'ldap://localhost:389/dc=example,dc=com??sub?(cn=John Doe)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));

        $url      = 'ldap://localhost:389/dc=example,dc=com?cn??(cn=John Doe)';
        $expected = 'ldap://localhost:389/dc=example,dc=com?cn?base?(cn=John Doe)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));

        $url      = 'ldap://localhost:389/dc=example,dc=com???(cn=John Doe)';
        $expected = 'ldap://localhost:389/dc=example,dc=com??base?(cn=John Doe)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));
    }

    /**
     * assure url roundtrip with params (attributes)
     *
     * @test
     */
    public function urlWithParamsAttributesValues()
    {
        $url      = 'ldap://localhost:389/dc=example,dc=com?cn';
        $expected = 'ldap://localhost:389/dc=example,dc=com?cn?base?(objectClass=*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));

        $url      = 'ldap://localhost:389/dc=example,dc=com?cn,uid';
        $expected = 'ldap://localhost:389/dc=example,dc=com?cn,uid?base?(objectClass=*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));
    }

    /**
     * assure url roundtrip with params (filter)
     *
     * @test
     */
    public function urlWithParamsFilterValues()
    {
        $url      = 'ldap://ldap.example.com:389/dc=example,dc=com???(cn=A*)';
        $expected = 'ldap://ldap.example.com:389/dc=example,dc=com??base?(cn=A*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));
    }

    /**
     * assure url roundtrip with params (scope)
     *
     * @test
     */
    public function urlWithParamsScopeValues()
    {
        $url      = 'ldap://ldap.example.com:389/dc=example,dc=com??base';
        $expected = 'ldap://ldap.example.com:389/dc=example,dc=com??base?(objectClass=*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));

        $url      = 'ldap://ldap.example.com:389/dc=example,dc=com??one';
        $expected = 'ldap://ldap.example.com:389/dc=example,dc=com??one?(objectClass=*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));

        $url      = 'ldap://ldap.example.com:389/dc=example,dc=com??sub';
        $expected = 'ldap://ldap.example.com:389/dc=example,dc=com??sub?(objectClass=*)';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));
    }

    /**
     * assure url roundtrip with ssl port
     *
     * @test
     */
    public function urlSSL()
    {
        $url  = 'ldaps://ldap.example.com:636/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($url, $ldap->get(true));

        $url      = 'ldaps://ldap.example.com/dc=example,dc=com';
        $expected = 'ldaps://ldap.example.com:636/dc=example,dc=com';
        $ldap = stubLDAPURL::fromString($url);
        $this->assertEquals($expected, $ldap->get(true));
    }

    /**
     * assure url with empty string returns
     *
     * @test
     */
    public function urlBadFormatEmptyString()
    {
        $this->assertNull(stubLDAPURL::fromString(''));
    }

    /**
     * data provider for assurance of throwing stubMalformedURLException
     *
     * @return  array<array<string>>
     */
    public static function malformedUrlProvider()
    {
        return array(
            // no ldap://
            array('ldap.example.com/dc=example,dc=com'),
            // bad scheme
            array('badScheme://ldap.example.com/dc=example,dc=com'),
            // bad base dn
            array('ldap://ldap.example.com/dc=example|dc=com'),
            // bad scope
            array('ldap://ldap.example.com/dc=example,dc=com??badScope)')
        );
    }

    /**
     * assure url roundtrip with ssl port
     *
     * @param  string  $url
     *
     * @test
     * @dataProvider       malformedUrlProvider
     * @expectedException  stubMalformedURLException
     */
    public function urlBadFormat($url)
    {
        stubLDAPURL::fromString($url);
    }
}
?>