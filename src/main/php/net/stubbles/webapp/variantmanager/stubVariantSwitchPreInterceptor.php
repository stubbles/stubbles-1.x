<?php
/**
 * Interceptor which allows switching of variants.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubVariantSwitchPreInterceptor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubMode',
                      'net::stubbles::webapp::variantmanager::stubVariantSettingPreInterceptor'
);
/**
 * Interceptor which allows switching of variants.
 *
 * A variant can be switched in STAGE or DEV mode only. The variant to switch to
 * has to be given with the __variant parameter and must be the fully qualified
 * variant name.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 */
class stubVariantSwitchPreInterceptor extends stubVariantSettingPreInterceptor
{
    /**
     * runtime mode
     *
     * @var  stubMode
     */
    protected $mode;

    /**
     * sets current runtime mode
     *
     * @param  stubMode  $mode
     * @Inject(optional=true)
     */
    public function setMode(stubMode $mode)
    {
        $this->mode = $mode;
    }

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
        if ($this->isAllowed() === false || $request->hasParam('__variant') === false) {
            return false;
        }

        return true;
    }

    /**
     * checks whether switching the variant is allowed within current context
     *
     * Switching the variant is possible if the application is running in STAGE
     * or DEV mode. If no mode is available it is assumed the application runs
     * in PROD mode.
     *
     * @return  bool
     */
    protected function isAllowed()
    {
        if (null === $this->mode) {
            return false;
        }

        if ($this->mode->name() !== 'STAGE' && $this->mode->name() !== 'DEV') {
            return false;
        }

        return true;
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
        $variantName = $request->readParam('__variant')->ifIsOneOf($this->variantFactory->getVariantNames());
        if (null == $variantName) {
            return null;
        }

        return $this->variantFactory->getVariantByName($variantName);
    }
}
?>