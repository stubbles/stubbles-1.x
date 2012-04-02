<?php
/**
 * Test for net::stubbles::peer::http::stubLDAPEntryTestCase.
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @version     $Id: stubLDAPEntryTestCase.php 2662 2010-08-23 15:55:21Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::ldap::stubLDAPEntry',
                      'net::stubbles::peer::ldap::stubLDAPURL'
);
/**
 * Test for net::stubbles::peer::http::stubLDAPEntryTestCase.
 *
 * Preconditions:
 *  - db.debian.org (LDAP Server)
 *  - uid=aaron,ou=users,dc=debian,dc=org (dn exists & attributes of entry exist)
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @group       peer
 * @group       peer_ldap
 */
class stubLDAPEntryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLDAPEntry
     */
    protected $entry;
    /**
     * LDAP connection
     *
     * @var  stubLDAPConnection
     */
    protected $ldap;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('ldap') === false) {
            $this->markTestSkipped('The LDAP extension is not available.');
        }

        $url = 'ldap://db.debian.org/ou=users,dc=debian,dc=org??sub?(sn=Howell)';
        $this->entry = stubLDAPURL::fromString($url)->connect()->bind()->search()->getEntry();
    }

    /**
     * assure getter functionality with good input
     *
     * @test
     */
    public function getterSuccessful()
    {
        $this->assertEquals(array('inetOrgPerson',
                                  'debianAccount',
                                  'shadowAccount',
                                  'debianDeveloper'), $this->entry->getObjectClassValues());

        $attributesCount = count($this->entry->getAttributes());
        $this->assertGreaterThan(10, $attributesCount);
        $this->assertLessThan(20, $attributesCount);

        $this->assertEquals(array('Howell'), $this->entry->getAttributeValuesByName('sn'));

        $this->assertTrue(is_array($this->entry->getAttributeNames()));
        $this->assertEquals($attributesCount, count($this->entry->getAttributeNames()));

        $attributeCount = $this->entry->getAttributeCount();
        $this->assertGreaterThan(10, $attributeCount);
        $this->assertLessThan(20, $attributeCount);

        $this->assertEquals('uid=aaron,ou=users,dc=debian,dc=org', $this->entry->getDn());
    }

    /**
     * assure getter functionality with bad input
     *
     * @test
     */
    public function getterFailures()
    {
        $this->assertNull($this->entry->getAttributeValuesByName('iDontExist'));
    }
}
?>