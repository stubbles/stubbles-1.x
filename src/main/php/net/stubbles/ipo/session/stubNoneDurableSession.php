<?php
/**
 * Session class that is not durable for more than one request.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 * @version     $Id: stubNoneDurableSession.php 2430 2009-12-28 17:13:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubAbstractSession');
/**
 * Session class that is not durable for more than one request.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 */
class stubNoneDurableSession extends stubAbstractSession
{
    /**
     * the session id
     *
     * @var  int
     */
    protected $id;
    /**
     * the data
     *
     * @var  array
     */
    protected $data           = array();

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
        $this->id = md5(uniqid(rand(), true));
        return true;
    }

    /**
     * returns fingerprint for user: not possible, as session lasts only for single request
     * 
     * @return  string
     */
    protected function getFingerprint()
    {
        return '';
    }

    /**
     * returns session id
     *
     * @return  string  the session id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * regenerates the session id but leaves session data
     *
     * @param   string       $sessionId  optional  new session id to be used
     * @return  stubSession
     */
    public function regenerateId($sessionId = null)
    {
        $this->id = $sessionId;
        if (null == $this->id) {
            $this->id = md5(uniqid(rand(), true));
        }
        
        return $this;
    }

    /**
     * invalidates current session and creates a new one
     */
    public function invalidate()
    {
        $this->data = array();
    }

    /**
     * stores a value associated with the key
     *
     * @param  string  $key    key to store value under
     * @param  mixed   $value  data to store
     */
    protected function doPutValue($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * returns a value associated with the key or the default value
     *
     * @param   string  $key  key where value is stored under
     * @return  mixed
     */
    protected function doGetValue($key)
    {
        return $this->data[$key];
    }

    /**
     * checks whether a value associated with key exists
     *
     * @param   string  $key  key where value is stored under
     * @return  bool
     */
    public function hasValue($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * removes a value from the session
     *
     * @param   string  $key  key where value is stored under
     * @return  bool    true if value existed and was removed, else false
     */
    protected function doRemoveValue($key)
    {
        unset($this->data[$key]);
        return true;
    }

    /**
     * return an array of all keys registered in this session
     *
     * @return  array<string>
     */
    protected function doGetValueKeys()
    {
        return array_keys($this->data);
    }
}
?>