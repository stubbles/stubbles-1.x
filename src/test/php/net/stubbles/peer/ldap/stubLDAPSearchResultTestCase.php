<?php
/**
 * Test for net::stubbles::peer::http::stubLDAPSearchResultTestCase.php.
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @version     $Id: stubLDAPSearchResultTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::ldap::stubLDAPSearchResult',
                      'net::stubbles::peer::ldap::stubLDAPURL'
);
/**
 * Test for net::stubbles::peer::http::stubLDAPSearchResultTestCase.php.
 *
 * Preconditions:
 *  - db.debian.org (LDAP Server)
 *  - ou=users,dc=debian,dc=org (dn exists)
 *
 * @package     stubbles
 * @subpackage  peer_ldap_test
 * @group       peer
 * @group       peer_ldap
 */
class stubLDAPSearchResultTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLDAPSearchResult
     */
    protected $searchResult;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('ldap') === false) {
            $this->markTestSkipped('The LDAP extension is not available.');
        }


        $url = 'ldap://db.debian.org/ou=users,dc=debian,dc=org??sub?(sn=A*)';
        $this->searchResult = stubLDAPURL::fromString($url)->connect()->bind()->search();
    }

    /**
     * assure getting an entry (valid(), next(), getEntry(), rewind())
     *
     * @test
     */
    public function getEntryNextValidRewind()
    {
        $this->assertInstanceOf('stubLDAPEntry', $this->searchResult->getEntry());
        for(;$this->searchResult->valid(); $this->searchResult->next()) {
            $this->assertInstanceOf('stubLDAPEntry', $this->searchResult->getEntry());
        }
        $this->assertNull($this->searchResult->getEntry());

        $this->searchResult->rewind();
        $this->assertInstanceOf('stubLDAPEntry', $this->searchResult->getEntry());
    }

    /**
     * assure getting the current item
     *
     * @test
     */
    public function current()
    {
        $this->assertEquals('resource', gettype($this->searchResult->current()));
    }

    /**
     * assure getting the key of an item
     *
     * @test
     */
    public function key()
    {
        $this->assertEquals('resource', gettype($this->searchResult->key()));
    }
}
?>