<?php
/**
 * Decorator for sessions which throw exceptions on their creation to ensure
 * that the site will still work by falling back to another session implementation.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 * @version     $Id: stubFallbackSession.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubNoneDurableSession',
                      'net::stubbles::ipo::session::stubSession'
);
/**
 * Decorator for sessions which throw exceptions on their creation to ensure
 * that the site will still work by falling back to another session implementation.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 */
abstract class stubFallbackSession extends stubBaseObject implements stubSession
{
    /**
     * decorated session
     *
     * @var  stubSession
     */
    protected $session;

    /**
     * constructor
     * 
     * @param  stubRequest   $request      request instance
     * @param  stubResponse  $response     response instance
     * @param  string        $sessionName  name of the session
     */
    public final function __construct(stubRequest $request, stubResponse $response, $sessionName)
    {
        try {
            $this->session = $this->doConstruct($request, $response, $sessionName);
        } catch (stubException $e) {
            $this->session = new stubNoneDurableSession($request, $response, $sessionName);
        }
    }

    /**
     * creates the decorated and possibly exception-throwing session instance
     *
     * @param   stubRequest   $request      request instance
     * @param   stubResponse  $response     response instance
     * @param   string        $sessionName  name of the session
     * @return  stubSession
     */
    protected abstract function doConstruct(stubRequest $request, stubResponse $response, $sessionName);

    /**
     * checks whether session has been started
     * 
     * Typically, a session is new on the first request of a user,
     * afterwards it should never be new.
     *
     * @return  bool  true if session has been started, else false
     */
    public function isNew()
    {
        return $this->session->isNew();
    }

    /**
     * returns unix timestamp when session was started
     *
     * @return  int
     */
    public function getStartTime()
    {
        return $this->session->getStartTime();
    }

    /**
     * returns session id
     *
     * @return  string  the session id
     */
    public function getId()
    {
        return $this->session->getId();
    }

    /**
     * returns the name of the session
     *
     * @return  string
     */
    public function getName()
    {
        return $this->session->getName();
    }

    /**
     * regenerates the session id but leaves session data
     *
     * @param   string       $sessionId  optional  new session id to be used
     * @return  stubSession
     */
    public function regenerateId($sessionId = null)
    {
        $this->session->regenerateId($sessionId);
        return $this;
    }

    /**
     * returns token of current request
     *
     * @return  string  the token
     */
    public function getCurrentToken()
    {
        return $this->session->getCurrentToken();
    }

    /**
     * returns token for next request
     *
     * @return  string  the token
     */
    public function getNextToken()
    {
        return $this->session->getNextToken();
    }

    /**
     * checks if this session is valid
     *
     * @return  bool
     */
    public function isValid()
    {
        return $this->session->isValid();
    }

    /**
     * invalidates current session and creates a new one
     */
    public function invalidate()
    {
        $this->session->invalidate();
    }

    /**
     * resets the session and deletes all session data
     *
     * @return  int
     */
    public function reset()
    {
        return $this->session->reset();
    }

    /**
     * stores a value associated with the key
     *
     * @param  string  $key    key to store value under
     * @param  mixed   $value  data to store
     */
    public function putValue($key, $value)
    {
        $this->session->putValue($key, $value);
    }

    /**
     * returns a value associated with the key or the default value
     *
     * @param   string  $key      key where value is stored under
     * @param   mixed   $default  optional  return this if no data is associated with $key
     * @return  mixed
     */
    public function getValue($key, $default = null)
    {
        return $this->session->getValue($key, $default);
    }

    /**
     * checks whether a value associated with key exists
     *
     * @param   string  $key  key where value is stored under
     * @return  bool
     */
    public function hasValue($key)
    {
        return $this->session->hasValue($key);
    }

    /**
     * removes a value from the session
     *
     * @param   string  $name  key where value is stored under
     * @return  bool    true if value existed and was removed, else false
     */
    public function removeValue($name)
    {
        return $this->session->removeValue($name);
    }

    /**
     * return an array of all keys registered in this session
     *
     * @return  array<string>
     */
    public function getValueKeys()
    {
        return $this->session->getValueKeys();
    }
}
?>