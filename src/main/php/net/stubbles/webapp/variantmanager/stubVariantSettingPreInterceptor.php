<?php
/**
 * Base class for pre interceptors which set the variant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubVariantSettingPreInterceptor.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubPreInterceptor',
                      'net::stubbles::webapp::variantmanager::stubVariantsCookieCreator',
                      'net::stubbles::webapp::variantmanager::stubVariantFactory'
);
/**
 * Base class for pre interceptors which set the variant.
 *
 * The choosen variant can be accessed within the session by the key
 * net.stubbles.webapp.variantmanager.variant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 */
abstract class stubVariantSettingPreInterceptor extends stubBaseObject implements stubPreInterceptor
{
    /**
     * variant factory to be used for creating the variant
     *
     * @var  stubVariantFactory
     */
    protected $variantFactory;
    /**
     * class to use for creating variant cookies
     *
     * @var  stubVariantsCookieCreator
     */
    protected $variantCookieCreator;

    /**
     * constructor
     *
     * @param  stubVariantFactory         $variantFactory
     * @param  stubVariantsCookieCreator  $variantCookieCreator
     * @Inject
     */
    public function __construct(stubVariantFactory $variantFactory, stubVariantsCookieCreator $variantCookieCreator)
    {
        $this->variantFactory       = $variantFactory;
        $this->variantCookieCreator = $variantCookieCreator;
    }

    /**
     * does the preprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function preProcess(stubRequest $request, stubSession $session, stubResponse $response)
    {
        if ($this->canSelectVariant($request, $session, $response) === false) {
            return;
        }

        $variant = $this->selectVariant($request, $session, $response);
        if (null === $variant) {
            return;
        }

        $session->putValue('net.stubbles.webapp.variantmanager.variant.name', $variant->getFullQualifiedName());
        $session->putValue('net.stubbles.webapp.variantmanager.variant.alias', $variant->getAlias());
        $response->addCookie($this->variantCookieCreator->createVariantCookie($variant->getFullQualifiedName()));
        $response->addCookie($this->variantCookieCreator->createMapCookie($this->variantFactory->getVariantsMapName()));
    }

    /**
     * checks if there is enough data to select a variant
     *
     * @param   stubRequest   $request   access to request data
     * @param   stubSession   $session   access to session data
     * @param   stubResponse  $response  access to response data
     * @return  bool
     */
    protected abstract function canSelectVariant(stubRequest $request, stubSession $session, stubResponse $response);

    /**
     * selects variant based on request and session data
     *
     * If no variant can be selected it is allowed to return null.
     *
     * @param   stubRequest   $request   access to request data
     * @param   stubSession   $session   access to session data
     * @param   stubResponse  $response  access to response data
     * @return  stubVariant
     */
    protected abstract function selectVariant(stubRequest $request, stubSession $session, stubResponse $response);
}
?>