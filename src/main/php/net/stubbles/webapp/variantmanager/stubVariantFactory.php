<?php
/**
 * Interface for variant factories that create a variant map.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubVariantFactory.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::webapp::variantmanager::stubVariantConfigurationException',
                      'net::stubbles::webapp::variantmanager::stubVariantsMap'
);
/**
 * Interface for variant factories that create a variant map.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @ImplementedBy(net::stubbles::webapp::variantmanager::stubXmlVariantFactory.class)
 */
interface stubVariantFactory extends stubObject
{
    /**
     * Get all defined variants in this configuration
     * 
     * @return  array<string>
     */
    public function getVariantNames();

    /**
     * get a variant by its name
     *
     * @param   string       $variantName
     * @return  stubVariant
     */
    public function getVariantByName($variantName);

    /**
     * Checks, whether a variant is valid for the current request and the
     * session of the user.
     *
     * This method also checks, whether all parent variants of the
     * variant are valid, as the conditions should be inherited from
     * the parents.
     *
     * @param   string       $variantName
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  boolean
     * @since   1.5.0
     */
    public function isVariantValid($variantName, stubSession $session, stubRequest $request);

    /**
     * checks whether we should use persistence or not
     *
     * @return  boolean
     */
    public function shouldUsePersistence();

    /**
     * returns the matching variant based on the current request and the session
     * of the user
     *
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  stubVariant
     * @throws  stubVariantConfigurationException
     */
    public function getVariant(stubSession $session, stubRequest $request);

    /**
     * returns a variant that enforces to be used based on the session of the user
     * and the current request
     *
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  stubVariant
     */
    public function getEnforcingVariant(stubSession $session, stubRequest $request);

    /**
     * return the variant map
     *
     * @return  stubVariantsMap
     */
    public function getVariantsMap();

    /**
     * returns the name of the variant map
     *
     * @return  string
     * @since   1.5.0
     */
    public function getVariantsMapName();
}
?>