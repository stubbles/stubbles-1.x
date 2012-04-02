<?php
/**
 * Session class that is durable for more than one request but does not store any data between requests.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 * @version     $Id: stubNoneStoringSession.php 2897 2011-01-12 01:05:55Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubNoneDurableSession');
/**
 * Session class that is durable for more than one request but does not store any data between requests.
 *
 * @package     stubbles
 * @subpackage  ipo_session
 */
class stubNoneStoringSession extends stubNoneDurableSession
{
    /**
     * the response instance
     *
     * @var  stubResponse
     */
    protected $response;
    /**
     * regular expression to validate the session id
     *
     * @var  string
     */
    protected $sessionIdRegex = '/^([a-zA-Z0-9]{32})$/D';

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
        $this->response = $response;
        if ($request->hasParam($sessionName) === true) {
            $this->id   = $request->readParam($sessionName)->ifSatisfiesRegex($this->sessionIdRegex);
            $this->data = array(stubSession::START_TIME  => time(),
                                stubSession::FINGERPRINT => '',
                                stubSession::NEXT_TOKEN  => ''
                          );
        } elseif ($request->hasCookie($sessionName) === true) {
            $this->id   = $request->readCookie($sessionName)->ifSatisfiesRegex($this->sessionIdRegex);
            $this->data = array(stubSession::START_TIME  => time(),
                                stubSession::FINGERPRINT => '',
                                stubSession::NEXT_TOKEN  => ''
                          );
        } else {
            parent::doConstruct($request, $response, $sessionName);
        }
        
        $this->response->addCookie(stubCookie::create($this->sessionName, $this->id)
                                             ->forPath('/')
                                             ->usingHttpOnly(true)
        );
        return true;
    }

    /**
     * regenerates the session id but leaves session data
     *
     * @param   string       $sessionId  optional  new session id to be used
     * @return  stubSession
     */
    public function regenerateId($sessionId = null)
    {
        parent::regenerateId($sessionId);
        $this->response->addCookie(stubCookie::create($this->sessionName, $this->id)
                                             ->forPath('/')
                                             ->usingHttpOnly(true)
        );
        return $this;
    }

    /**
     * invalidates current session and creates a new one
     */
    public function invalidate()
    {
        parent::invalidate();
        $this->response->addCookie(stubCookie::create($this->sessionName, $this->id)
                                             ->forPath('/')
                                             ->usingHttpOnly(true)
                                             ->expiringAt(time() - 86400)
        );
    }
}
?>