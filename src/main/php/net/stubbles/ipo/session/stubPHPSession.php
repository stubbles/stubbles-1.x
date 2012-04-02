<?php
/**
 * Session class using default PHP sessions.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 * @version     $Id: stubPHPSession.php 2626 2010-08-12 17:05:15Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubAbstractSession',
                      'net::stubbles::php::string::stubMd5Encoder'
);
/**
 * Session class using default PHP sessions.
 *
 * This session class offers session handling based on the default PHP session
 * functions.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 * @uses        http://php.net/session
 */
class stubPHPSession extends stubAbstractSession
{
    /**
     * the request instance
     *
     * @var  stubRequest
     */
    protected $request;

    /**
     * template method for child classes to do the real construction
     *
     * @param   stubRequest   $request      request instance
     * @param   stubResponse  $response     response instance
     * @param   string        $sessionName  name of the session
     * @return  bool
     */
    protected function doConstruct(stubRequest $request, stubResponse $response, $sessionName)
    {
        $this->request = $request;
        session_name($sessionName);
        @session_start();
        return true;
    }

    /**
     * returns fingerprint for user: has to use same user agent all over the session
     *
     * @return  string
     */
    protected function getFingerprint()
    {
        $encoder = new stubMd5Encoder();
        return $encoder->encode($this->request->readHeader('HTTP_USER_AGENT')->unsecure());
    }

    /**
     * returns session id
     *
     * @return  string  the session id
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * regenerates the session id but leaves session data
     *
     * @param   string       $sessionId  optional  new session id to be used
     * @return  stubSession
     */
    public function regenerateId($sessionId = null)
    {
        @session_regenerate_id(true);
    }

    /**
     * invalidates current session and creates a new one
     */
    public function invalidate()
    {
        $_SESSION = array();
        @session_destroy();
        @session_start();
    }

    /**
     * stores a value associated with the key
     *
     * @param  string  $key    key to store value under
     * @param  mixed   $value  data to store
     */
    protected function doPutValue($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * returns a value associated with the key or the default value
     *
     * @param   string  $key  key where value is stored under
     * @return  mixed
     */
    protected function doGetValue($key)
    {
        return $_SESSION[$key];
    }

    /**
     * checks whether a value associated with key exists
     *
     * @param   string  $key  key where value is stored under
     * @return  bool
     */
    public function hasValue($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * removes a value from the session
     *
     * @param   string  $key  key where value is stored under
     * @return  bool    true if value existed and was removed, else false
     */
    protected function doRemoveValue($key)
    {
        unset($_SESSION[$key]);
        return true;
    }

    /**
     * return an array of all keys registered in this session
     *
     * @return  array<string>
     */
    protected function doGetValueKeys()
    {
        return array_keys($_SESSION);
    }
}
?>