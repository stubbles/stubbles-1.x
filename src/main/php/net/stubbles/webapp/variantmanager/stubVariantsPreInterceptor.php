<?php
/**
 * Pre interceptor for selecting the variant based on request data.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubVariantsPreInterceptor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubVariantSettingPreInterceptor');
/**
 * Pre interceptor for selecting the variant based on request data.
 *
 * If there is already a selected variant within the session no further
 * selection will take place.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 */
class stubVariantsPreInterceptor extends stubVariantSettingPreInterceptor
{
    /**
     * checks if there is enough data to select a variant
     *
     * @param   stubRequest   $request   access to request data
     * @param   stubSession   $session   access to session data
     * @param   stubResponse  $response  access to response data
     * @return  bool
     */
    protected function canSelectVariant(stubRequest $request, stubSession $session, stubResponse $response)
    {
        return (false === $session->hasValue('net.stubbles.webapp.variantmanager.variant.name'));
    }

    /**
     * selects variant based on request and session data
     *
     * @param   stubRequest   $request   access to request data
     * @param   stubSession   $session   access to session data
     * @param   stubResponse  $response  access to response data
     * @return  stubVariant
     */
    protected function selectVariant(stubRequest $request, stubSession $session, stubResponse $response)
    {
        $variant = null;
        if ($this->variantFactory->shouldUsePersistence() === true) {
            $variant = $this->getVariantFromCookie($request, $session);
        }
        
        if (null === $variant) {
            $variant = $this->variantFactory->getVariant($session, $request);
        }
        
        return $variant;
    }

    /**
     * tries to get the variant from the cookie
     *
     * @param   stubRequest  $request  access to request data
     * @param   stubSession  $session  access to session data
     * @return  stubVariant
     */
    protected function getVariantFromCookie(stubRequest $request, stubSession $session)
    {
        if ($request->hasCookie($this->variantCookieCreator->getCookieName()) === false) {
            return null;
        }

        if ($request->validateCookie($this->variantCookieCreator->getCookieMapName())
                    ->isEqualTo($this->variantFactory->getVariantsMapName()) === false) {
            return null;
        }

        $variantName = $request->readCookie($this->variantCookieCreator->getCookieName())
                               ->ifIsOneOf($this->variantFactory->getVariantNames());
        if (null == $variantName) {
            return null;
        }
        
        if ($this->variantFactory->isVariantValid($variantName, $session, $request) === false) {
            return null;
        }

        $cookieVariant    = $this->variantFactory->getVariantByName($variantName);
        $enforcingVariant = $this->variantFactory->getEnforcingVariant($session, $request);
        if (null === $enforcingVariant) {
            return $cookieVariant;
        }
        
        if (substr($cookieVariant->getFullQualifiedName(), 0, strlen($enforcingVariant->getFullQualifiedName())) === $enforcingVariant->getFullQualifiedName()) {
            return $cookieVariant;
        }
        
        return $enforcingVariant;
    }
}
?>