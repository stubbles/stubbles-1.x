<?php
/**
 * Abstract base implementation for a variant factory.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubAbstractVariantFactory.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubVariantFactory',
                      'net::stubbles::webapp::variantmanager::stubVariantsMap'
);
/**
 * Abstract base implementation for a variant factory.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 */
abstract class stubAbstractVariantFactory extends stubBaseObject implements stubVariantFactory
{
    /**
     * map of available variants
     *
     * @var  stubVariantsMap
     */
    private $variantsMap;

    /**
     * Get all defined variants in this configuration
     * 
     * @return  array<string>
     */
    public function getVariantNames()
    {
        return $this->getVariantsMap()->getVariantNames();
    }

    /**
     * get a variant by its name
     *
     * @param   string       $variantName
     * @return  stubVariant
     */
    public function getVariantByName($variantName)
    {
        return $this->getVariantsMap()->getVariantByName($variantName);
    }

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
    public function isVariantValid($variantName, stubSession $session, stubRequest $request)
    {
        return $this->getVariantsMap()->isVariantValid($variantName, $session, $request);
    }

    /**
     * checks whether we should use persistence or not
     *
     * @return  boolean
     * @since   1.1.0
     */
    public function shouldUsePersistence()
    {
        return $this->getVariantsMap()->shouldUsePersistence();
    }

    /**
     * returns the matching variant based on the current request and the session
     * of the user
     *
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  stubVariant
     * @since   1.1.0
     */
    public function getVariant(stubSession $session, stubRequest $request)
    {
        return $this->getVariantsMap()->getVariant($session, $request);
    }

    /**
     * returns a variant that enforces to be used based on the session of the user
     * and the current request
     *
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  stubVariant
     * @since   1.1.0
     */
    public function getEnforcingVariant(stubSession $session, stubRequest $request)
    {
        return $this->getVariantsMap()->getEnforcingVariant($session, $request);
    }

    /**
     * return the variant map
     *
     * @return  stubVariantsMap
     */
    public function getVariantsMap()
    {
        if (null === $this->variantsMap) {
            $this->variantsMap = $this->createVariantsMap();
        }

        return $this->variantsMap;
    }

    /**
     * creates the variants map
     *
     * @return  stubVariantsMap
     */
    protected abstract function createVariantsMap();

    /**
     * returns the name of the variant map
     *
     * @return  string
     * @since   1.5.0
     */
    public function getVariantsMapName()
    {
        return $this->getVariantsMap()->getName();
    }
}
?>