<?php
/**
 * All built-in scopes.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubBindingScopes.php 2967 2011-02-07 18:08:45Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBindingScopeSession',
                      'net::stubbles::ioc::stubBindingScopeSingleton'
);
/**
 * All built-in scopes.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubBindingScopes extends stubBaseObject
{
    /**
     * scope for singleton objects
     *
     * @var  stubBindingScope
     */
    protected $singletonScope;
    /**
     * scope for session resources
     *
     * @var  stubBindingScopeSession
     */
    protected $sessionScope;

    /**
     * constructor
     *
     * @param  stubBindingScope  $singletonScope  optional
     * @param  stubBindingScope  $sessionScope    optional
     * @since  1.5.0
     */
    public function  __construct(stubBindingScope $singletonScope = null, stubBindingScope $sessionScope = null)
    {
        if (null === $singletonScope) {
            $this->singletonScope = new stubBindingScopeSingleton();
        } else {
            $this->singletonScope = $singletonScope;
        }

        if (null === $sessionScope) {
            $this->sessionScope = new stubBindingScopeSession();
        } else {
            $this->sessionScope = $sessionScope;
        }
    }

    /**
     * returns scope for singleton objects
     *
     * @return  stubBindingScope
     * @since   1.5.0
     */
    public function getSingletonScope()
    {
        return $this->singletonScope;
    }

    /**
     * sets session to be used with the session scope
     *
     * @param   stubSession        $session
     * @return  stubBindingScopes
     * @since   1.5.0
     */
    public function setSessionForSessionScope(stubSession $session)
    {
        $this->sessionScope->setSession($session);
        return $this;
    }

    /**
     * returns scope for session resources
     *
     * @return  stubBindingScope
     * @since   1.5.0
     */
    public function getSessionScope()
    {
        return $this->sessionScope;
    }
}
?>