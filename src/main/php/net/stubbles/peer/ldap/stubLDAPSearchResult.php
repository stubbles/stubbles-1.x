<?php
/**
 * Representation of a LDAP search result.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @version     $Id: stubLDAPSearchResult.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::ldap::stubLDAPEntry');
/**
 * Representation of a LDAP search result.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 */
class stubLDAPSearchResult extends stubBaseObject implements Iterator
{
    /**
     * ldap connection identifier
     *
     * @var  resource
     */
    protected $handle;
    /**
     * ldap result identifier
     *
     * @var  resource
     */
    protected $result;
    /**
     * current ldap entryId
     *
     * @var  ressource
     */
    protected $current;

    /**
     * constructor (creates stubLDAPEntry objects)
     *
     * Unset calls remove the count value for better looping
     * and because this value is ascertainable later.
     *
     * @param  resource  $handle  ldap connection identifier
     * @param  resource  $result  ldap result identifier
     */
    public function __construct($handle, $result)
    {
        $this->handle  = $handle;
        $this->result  = $result;
        $entry = ldap_first_entry($handle, $result);
        $this->current = ($entry !== false) ? $entry : null;
    }

    /**
     * Provides a stubLDAPEntry.
     *
     * @param   int  $entryId  ldap entry identifier
     * @return  stubLDAPEntry
     */
    protected function provideLDAPEntry($entryId)
    {
        $attributes = ldap_get_attributes($this->handle, $entryId);

        $entryAttr   = array();
        $objectClass = array();
        foreach ($attributes as $name => $values) {
            // reject special attributes
            if($name === 'count' || is_int($name)) {
                continue;
            }

            // get rid of amount
            if(isset($values['count'])) {
                unset($values['count']);
            }

            // get object class values
            if($name === 'objectClass') {
                $objectClass = $values;
                continue;
            }

            // save 'normal' attributes
            $entryAttr[$name] = $values;
        }

        $dn = ldap_get_dn($this->handle, $entryId);
        if(empty($objectClass)) {
            $objectClass = ldap_read($this->handle, $dn, '(objectClass=*)', array('objectclass'));
        }

        $ldapEntry = new stubLDAPEntry(
                           $dn,
                           $objectClass,
                           $entryAttr,
                           $attributes['count']
                         );

        return $ldapEntry;
    }

    /**
     * Gets the current entry.
     *
     * @return  stubLDAPEntry
     */
    public function getEntry()
    {
        return ($this->current() !== null ? $this->provideLDAPEntry($this->current()) : null);
    }

    /**
     * Returns the current entry identifier.
     *
     * @return  ressource
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Returns the key of the current element.
     *
     * @return  ressource
     */
    public function key()
    {
        return $this->current();
    }

    /**
     * Moves forward to next element.
     */
    public function next()
    {
        $this->current = ($result = ldap_next_entry($this->handle, $this->current())) ? $result : null;
    }

    /**
     * Rewinds the Iterator to the first element.
     */
    public function rewind()
    {
        $this->current = ldap_first_entry($this->handle, $this->result);
    }

    /**
     * Checks if there is a current element after calls to rewind() or next().
     *
     * @return  boolean
     */
    public function valid()
    {
        return ($this->current() !== null) ? true : false;
    }
}
?>