<?php
/**
 * Interface for all variants.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 * @version     $Id: stubVariant.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::session::stubSession'
);
/**
 * Interface for all variants.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 */
interface stubVariant extends stubSerializable
{
    /**
     * returns the name of the variant
     * 
     * @return  string
     */
    public function getName();

    /**
     * returns the full qualified name of the variant
     *
     * @return  string
     */
    public function getFullQualifiedName();

    /**
     * returns title of the variant
     * 
     * @return  string
     */
    public function getTitle();

    /**
     * returns alias name of the variant
     * 
     * @return  string
     */
    public function getAlias();

    /**
     * check whether the variant is an enforcing variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  boolean
     */
    public function isEnforcing(stubSession $session, stubRequest $request);

    /**
     * return the forced variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  stubVariant
     */
    public function getEnforcingVariant(stubSession $session, stubRequest $request);

    /**
     * return the variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request
     * @return  stubVariant
     */
    public function getVariant(stubSession $session, stubRequest $request);

    /**
     * check whether the conditions for this variant are met
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  boolean
     */
    public function conditionsMet(stubSession $session, stubRequest $request);

    /**
     * check whether the variant is valid
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  boolean
     */
    public function isValid(stubSession $session, stubRequest $request);

    /**
     * assign that this variant has been choosen
     * 
     * @param  stubSession  $session  access to session
     * @param  stubRequest  $request  access to request parameters
     */
    public function assign(stubSession $session, stubRequest $request);

    /**
     * returns parent variant
     * 
     * @return  stubVariant
     */
    public function getParent();

    /**
     * check whether the variant has a parent variant
     * 
     * @return  boolean
     */
    public function hasParent();

    /**
     * return child variants of this variant
     * 
     * @return  array<stubVariant>
     */
    public function getChildren();
}
?>