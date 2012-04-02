<?php
/**
 * Interface for configurable variants.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 * @version     $Id: stubConfigurableVariant.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubVariant');
/**
 * Interface for configurable variants.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 */
interface stubConfigurableVariant extends stubVariant
{
    /**
     * sets the name of the variant
     *
     * @param   string                   $name
     * @return  stubConfigurableVariant
     */
    public function setName($name);

    /**
     * sets the title of the variant
     *
     * @param   string                   $title
     * @return  stubConfigurableVariant
     */
    public function setTitle($title);

    /**
     * set alias name of the variant
     *
     * @param   string                   $alias
     * @return  stubConfigurableVariant
     */
    public function setAlias($alias);

    /**
     * add a child variant
     * 
     * @param   stubConfigurableVariant  $child
     * @return  stubConfigurableVariant
     */
    public function addChild(stubConfigurableVariant $child);

    /**
     * set parent variant
     *
     * @param   stubVariant              $parent
     * @return  stubConfigurableVariant
     */
    public function setParent(stubVariant $parent = null);
}
?>