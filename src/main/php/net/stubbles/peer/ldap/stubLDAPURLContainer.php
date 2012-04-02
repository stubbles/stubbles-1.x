<?php
/**
 * Container for LDAP urls.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @version     $Id: stubLDAPURLContainer.php 2350 2009-10-09 11:57:08Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubURLContainer');
/**
 * Container for LDAP urls.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @see         RFC 4510  LDAP: Technical Specification Road Map              http://tools.ietf.org/html/rfc4510
 * @see         RFC 4514  LDAP: String Representation of Distinguished Names  http://tools.ietf.org/html/rfc4514
 * @see         RFC 4516  LDAP: Uniform Resource Locator                      http://tools.ietf.org/html/rfc4516
 * @see         RFC 4515  LDAP: String Representation of Search Filters       http://tools.ietf.org/html/rfc4515
 */
interface stubLDAPURLContainer extends stubURLContainer
{
    /**
     * Gets the base dn (distinguished name).
     *
     * @return  string
     */
    public function getBaseDn();

    /**
     * Changes the originally used base dn (distinguished name).
     *
     * @param  string  $newBaseDn
     */
    public function setBaseDn($newBaseDn);

    /**
     * Returns a stubLDAPConnection.
     *
     * @return  stubLDAPConnection
     */
    public function connect();
}
?>