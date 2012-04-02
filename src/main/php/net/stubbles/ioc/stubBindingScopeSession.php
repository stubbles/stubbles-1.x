<?php
/**
 * Scope for session-bounded singletons.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubBindingScopeSession.php 3012 2011-02-17 14:19:49Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBindingScope',
                      'net::stubbles::ipo::session::stubSession'
);
/**
 * Scope for session-bounded singletons.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubBindingScopeSession extends stubBaseObject implements stubBindingScope
{
    /**
     * session prefix key
     */
    const SESSION_KEY    = 'net.stubbles.ioc.sessionScope#';
    /**
     * session instance to store instances in
     *
     * @var  stubSession
     */
    protected $session;
    /**
     * instances in this scope
     *
     * @var  array<string,object>
     */
    protected $instances = array();

    /**
     * sets the session
     *
     * @param  stubSession  $session
     */
    public function setSession(stubSession $session)
    {
        $this->session = $session;
    }

    /**
     * returns the requested instance from the scope
     *
     * @param   stubBaseReflectionClass  $type      type of the object
     * @param   stubBaseReflectionClass  $impl      concrete implementation
     * @param   stubInjectionProvider    $provider
     * @return  object
     * @throws  stubRuntimeException
     */
    public function getInstance(stubBaseReflectionClass $type, stubBaseReflectionClass $impl, stubInjectionProvider $provider)
    {
        if (null === $this->session) {
            throw new stubRuntimeException('No instance of net::stubbles::ipo::session::stubSession available.');
        }
        
        $key = self::SESSION_KEY . $impl->getName();
        if (isset($this->instances[$key]) === true) {
            return $this->instances[$key];
        }
        
        if ($this->session->hasValue($key) === true) {
            $this->instances[$key] = $this->session->getValue($key);
            return $this->instances[$key];
        }
        
        $this->instances[$key] = $provider->get();
        $this->session->putValue($key, $this->instances[$key]);
        return $this->instances[$key];
    }
}
?>