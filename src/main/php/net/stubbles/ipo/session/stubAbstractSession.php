<?php
/**
 * Base class for session implementations.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 * @version     $Id: stubAbstractSession.php 2886 2011-01-11 22:00:42Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::lang::exceptions::stubIllegalStateException',
                      'net::stubbles::lang::exceptions::stubRuntimeException'
);
/**
 * Base class for session implementations.
 *
 * This class offers a basic implementation for session handling, mainly for
 * the default values of a session which are the start time of the session,
 * the fingerprint of the user and the token of the current and the next
 * request. While a concrete instance is created the class checks the session
 * to prevent the user against session fixation and session hijacking.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 */
abstract class stubAbstractSession extends stubBaseObject implements stubSession
{
    /**
     * switch whether session is new or not
     *
     * @var  bool
     */
    protected $isNew       = false;
    /**
     * the current token of the session, changes on every instantiation
     *
     * @var  string
     */
    protected $token       = '';
    /**
     * name of the session
     *
     * @var  string
     */
    protected $sessionName = '';
    /**
     * cache for objects
     *
     * @var  array<string,object>
     */
    protected $objectCache = array();

    /**
     * constructor
     *
     * @param  stubRequest   $request      request instance
     * @param  stubResponse  $response     response instance
     * @param  string        $sessionName  name of the session
     */
    public function __construct(stubRequest $request, stubResponse $response, $sessionName)
    {
        $this->sessionName = $sessionName;
        if ($this->doConstruct($request, $response, $sessionName) === false) {
            return;
        }

        if ($this->hasValue(stubSession::START_TIME) == false || $this->doGetValue(stubSession::FINGERPRINT) != $this->getFingerprint()) {
            if ($this->hasValue(stubSession::START_TIME) == false) {
                // prevent session fixation
                $this->regenerateId();
            } else {
                // prevent session hijacking
                $this->invalidate();
            }

            $this->putValue(stubSession::START_TIME, time());
            $this->isNew = true;
            $this->putValue(stubSession::FINGERPRINT, $this->getFingerprint());
            $this->token = md5(uniqid(rand()));
        } else {
            $this->token = $this->doGetValue(stubSession::NEXT_TOKEN);
        }

        $this->putValue(stubSession::NEXT_TOKEN, md5(uniqid(rand())));
    }

    /**
     * template method for child classes to do the real construction
     *
     * @param   stubRequest   $request      request instance
     * @param   stubResponse  $response     response instance
     * @param   string        $sessionName  name of the session
     * @return  bool
     */
    protected abstract function doConstruct(stubRequest $request, stubResponse $response, $sessionName);

    /**
     * clear up object cache (make sure that changed objects are written into session store)
     */
    public final function __destruct()
    {
        foreach ($this->objectCache as $key => $value) {
            $this->managePutValue($key, $value);
        }

        $this->doDescruct();
    }

    /**
     * template method for child classes to do specific descruction
     */
    protected function doDescruct()
    {
        // intentionally empty
    }

    /**
     * returns fingerprint for user: has to use same user agent all over the session
     *
     * @return  string
     */
    protected abstract function getFingerprint();

    /**
     * cloning is forbidden
     *
     * @throws  stubRuntimeException
     */
    public final function __clone()
    {
        throw new stubRuntimeException('Cloning the session is somewhat... useless.');
    }

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
        return $this->isNew;
    }

    /**
     * returns unix timestamp when session was started
     *
     * @return  int
     * @throws  stubIllegalStateException
     */
    public function getStartTime()
    {
        if ($this->isValid() == false) {
            throw new stubIllegalStateException('Session is in an invalid state.');
        }

        return $this->getValue(stubSession::START_TIME);
    }

    /**
     * returns the name of the session
     *
     * @return  string
     */
    public function getName()
    {
        return $this->sessionName;
    }

    /**
     * returns token of current request
     *
     * @return  string  the token
     */
    public function getCurrentToken()
    {
        return $this->token;
    }

    /**
     * returns token for next request
     *
     * @return  string  the token
     */
    public function getNextToken()
    {
        return $this->getValue(stubSession::NEXT_TOKEN);
    }

    /**
     * checks if this session is valid
     *
     * @return  bool
     */
    public function isValid()
    {
        return $this->hasValue(stubSession::START_TIME);
    }

    /**
     * resets the session and deletes all session data
     *
     * @return  int
     */
    public function reset()
    {
        $this->objectCache = array();
        $valueKeys         = $this->getValueKeys();
        $count             = 0;
        foreach ($valueKeys as $valueKey) {
            if (stubSession::NEXT_TOKEN == $valueKey || stubSession::FINGERPRINT == $valueKey) {
                continue;
            }

            if (stubSession::START_TIME == $valueKey) {
                $this->putValue(stubSession::START_TIME, time());
                continue;
            }

            $count += (int) $this->doRemoveValue($valueKey);
        }

        return $count;
    }

    /**
     * stores a value associated with the key
     *
     * @param  string  $key    key to store value under
     * @param  mixed   $value  data to store
     */
    public function putValue($key, $value)
    {
        $this->managePutValue($key, $value);
        if (is_object($value) === true) {
            $this->objectCache[$key] = $value;
        }
    }

    /**
     * helper method for storing the value into the session
     *
     * @param  string  $key    key to store value under
     * @param  mixed   $value  data to store
     */
    protected function managePutValue($key, $value)
    {
        // This will enable lazy loading for stubObjects that are stored within
        // the session and implement the stubSerializable interface.
        if ($value instanceof stubSerializable) {
            $this->doPutValue($key, $value->getSerialized());
        } else {
            $this->doPutValue($key, $value);
        }
    }

    /**
     * returns a value associated with the key or the default value
     *
     * @param  string  $key    key to store value under
     * @param  mixed   $value  data to store
     */
    protected abstract function doPutValue($key, $value);

    /**
     * returns a value associated with the key or the default value
     *
     * @param   string  $key      key where value is stored under
     * @param   mixed   $default  optional  return this if no data is associated with $key
     * @return  mixed
     * @throws  stubIllegalStateException
     */
    public function getValue($key, $default = null)
    {
        if ($this->isValid() == false) {
            throw new stubIllegalStateException('Session is in an invalid state.');
        }

        if (isset($this->objectCache[$key]) === true) {
            return $this->objectCache[$key];
        }

        if ($this->hasValue($key) == true) {
            $value = $this->doGetValue($key);
            if ($value instanceof stubSerializedObject) {
                $this->objectCache[$key] = $value->getUnserialized();
                return $this->objectCache[$key];
            }

            if (is_object($value) === true) {
                $this->objectCache[$key] = $value;
            }

            return $value;
        }

        return $default;
    }

    /**
     * returns a value associated with the key or the default value
     *
     * @param   string  $key  key where value is stored under
     * @return  mixed
     */
    protected abstract function doGetValue($key);

    /**
     * removes a value from the session
     *
     * @param   string  $key  key where value is stored under
     * @return  bool    true if value existed and was removed, else false
     * @throws  stubIllegalStateException
     */
    public function removeValue($key)
    {
        if ($this->isValid() == false) {
            throw new stubIllegalStateException('Session is in an invalid state.');
        }

        if (isset($this->objectCache[$key]) === true) {
            unset($this->objectCache[$key]);
        }

        if ($this->hasValue($key) == true) {
            return $this->doRemoveValue($key);
        }

        return false;
    }

    /**
     * removes a value from the session
     *
     * @param   string  $key  key where value is stored under
     * @return  bool   true if value existed and was removed, else false
     */
    protected abstract function doRemoveValue($key);

    /**
     * return an array of all keys registered in this session
     *
     * @return  array<string>
     * @throws  stubIllegalStateException
     */
    public function getValueKeys()
    {
        if ($this->isValid() == false) {
            throw new stubIllegalStateException('Session is in an invalid state.');
        }

        $valueKeys = array();
        foreach ($this->doGetValueKeys() as $valueKey) {
            if (substr($valueKey, 0, 11) !== '__stubbles_') {
                $valueKeys[] = $valueKey;
            }
        }

        return $valueKeys;
    }

    /**
     * return an array of all keys registered in this session
     *
     * @return  array<string>
     */
    protected abstract function doGetValueKeys();
}
?>