<?php
/**
 * Representation of a LDAP entry.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @version     $Id: stubLDAPEntry.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Representation of a LDAP entry.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 */
class stubLDAPEntry extends stubBaseObject
{
    /**
     * attributes
     *
     * @var  array<string,array<string>>
     */
    protected $attributes;
    /**
     * objectClass attribute
     *
     * @var  array<string>
     */
    protected $objectClass;
    /**
     * amount of attributes (inclusive objectClass)
     *
     * @var  int
     */
    protected $attributeCount;
    /**
     * distinguished name (dn)
     *
     * @var  string
     */
    protected $dn;

    /**
     * constructor
     *
     * @param  string  $dn
     * @param  array   $objectClass
     * @param  array   $attributes
     * @param  int     $attributeCount
     */
    public function __construct($dn, $objectClass, $attributes, $attributeCount)
    {
        $this->dn             = $dn;
        $this->objectClass    = $objectClass;
        foreach ($attributes as $name => $values) {
            $this->attributes[$name] = $values;
        }
        $this->attributeCount = $attributeCount;    // with objectClass

    }

    /**
     * Gets the dn (distinguished name).
     *
     * @return  string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * Gets the objectClass values.
     *
     * @return  array<string>
     */
    public function getObjectClassValues()
    {
        return $this->objectClass;
    }

    /**
     * Gets the attributes.
     *
     * @return  array<string,array<string>>
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Gets the value(s) for an attribute.
     *
     * @param   string         $name
     * @return  array<string>
     */
    public function getAttributeValuesByName($name)
    {
        return (isset($this->attributes[$name]) !== false) ? $this->attributes[$name] : null;
    }

    /**
     * Gets the attribute names.
     *
     * @return  array<string>
     */
    public function getAttributeNames()
    {
        return array_keys($this->attributes);
    }

    /**
     * Gets amount of attributes (inclusive objectClass).
     *
     * @return  int
     */
    public function getAttributeCount()
    {
        return $this->attributeCount;
    }
}
?>