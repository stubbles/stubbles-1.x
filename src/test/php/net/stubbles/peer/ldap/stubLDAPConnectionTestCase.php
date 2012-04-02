<?php
/**
 * Test for net::stubbles::peer::http::stubLDAPConnectionTestCase.
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @version     $Id: stubLDAPConnectionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::ldap::stubLDAPConnection');
/**
 * Test for net::stubbles::peer::http::stubLDAPConnectionTestCase.
 *
 * Preconditions:
 *  - db.debian.org (LDAP Server)
 *  - ou=hosts,dc=debian,dc=org (dn exists)
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @group       peer
 * @group       peer_ldap
 */
class stubLDAPConnectionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLDAPConnection
     */
    public $ldap;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('ldap') === false) {
            $this->markTestSkipped('The LDAP extension is not available.');
        }

        $url = 'ldap://db.debian.org/ou=hosts,dc=debian,dc=org';
        $this->ldap = stubLDAPURL::fromString($url)->connect();
    }

    /**
     * tear down test environment
     */
    public function tearDown()
    {
        if (null !== $this->ldap) {
            $this->ldap->unbind();
        }
    }

    /**
     * invalid bind
     *
     * @test
     * @expectedException  stubConnectionException
     */
    public function invalidBind()
    {
        stubLDAPURL::fromString('ldap://badUser:pw@db.debian.org/ou=hosts,dc=debian,dc=org')->connect()->bind();
    }

    /**
     * ldap searches
     *
     * @test
     */
    public function searchSuccessfull()
    {
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search());
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search(null, 'base', null));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search(null, 'one', null));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search(null, 'sub', null));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search('objectClass', null, null));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search('objectclass', null, null));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search('iDontExist', null, null));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search(null, null, '(sn=A*)'));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search(null, null, '(sn=iDontExist)'));
        $this->assertInstanceOf('stubLDAPSearchResult', $this->ldap->bind()->search(null, null, '(&(abc=def)(def=ghi))'));
    }

    /**
     * ldap search with bad scope input
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function searchFailureBadScopeInput()
    {
        $this->ldap->bind()->search(null, 'notBaseOneOrSub', null);
    }

    /**
     * ldap search with bad filter input
     *
     * @test
     * @expectedException  stubConnectionException
     */
    public function searchFailureBadFilterInput()
    {
        $this->ldap->bind()->search(null, null, '(noEqualSign)');
    }

    /**
     * ldap search with bad base dn
     *
     * @test
     * @expectedException  stubConnectionException
     */
    public function searchWithBadBaseDn()
    {
        $this->ldap->setBaseDn('badDN');
        $this->ldap->bind()->search();
    }

    /**
     * assure setting of default protocol during bind()
     *
     * @test
     */
    public function defaultProtocolVersion()
    {
        $this->assertEquals(3, $this->ldap->bind()->getOption(LDAP_OPT_PROTOCOL_VERSION));
    }

    /**
     * assure option() and getOption()
     *
     * @test
     */
    public function protocolVersionAndOverriding()
    {
        $this->ldap->option(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->assertEquals(3, $this->ldap->getOption(LDAP_OPT_PROTOCOL_VERSION));
        $this->ldap->option(LDAP_OPT_PROTOCOL_VERSION, 2);
        $this->assertEquals(2, $this->ldap->getOption(LDAP_OPT_PROTOCOL_VERSION));
    }

    /**
     * assure option getting
     *
     * @test
     */
    public function validOptionGetting()
    {
        $this->assertInternalType('string', $this->ldap->getOption(LDAP_OPT_HOST_NAME));
        $this->assertInternalType('int', $this->ldap->getOption(LDAP_OPT_PROTOCOL_VERSION));
    }

    /**
     * invalid option setting
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidOptionSetting()
    {
        $this->ldap->option('badInput', 123);
    }

    /**
     * invalid option getting
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidOptionGetting()
    {
        $this->ldap->getOption('badInput');
    }

    /**
     * assure option validity
     *
     * @test
     */
    public function optionValidity()
    {
        $this->assertTrue($this->ldap->isOptionValid(LDAP_OPT_PROTOCOL_VERSION));
        $this->assertFalse($this->ldap->isOptionValid('badInput'));
    }
}
?>