<?php
/**
 * Base processor implementation.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 * @version     $Id: stubAbstractProcessor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::processor::stubProcessor');
/**
 * Base processor implementation.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 */
abstract class stubAbstractProcessor extends stubBaseObject implements stubProcessor
{
    /**
     * the request
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * current session
     *
     * @var  stubSession
     */
    protected $session;
    /**
     * the created response
     *
     * @var  stubResponse
     */
    protected $response;
    /**
     * switch whether we are running in ssl mode or not
     *
     * @var  bool
     */
    private $ssl        = null;

    /**
     * constructor
     *
     * @param  stubRequest   $request   current request
     * @param  stubSession   $session   current session
     * @param  stubResponse  $response  current response
     * @Inject
     */
    public function __construct(stubRequest $request, stubSession $session, stubResponse $response)
    {
        $this->request  = $request;
        $this->session  = $session;
        $this->response = $response;
    }

    /**
     * operations to be done before the request is processed
     *
     * @param   stubUriRequest  $uriRequest
     * @return  stubProcessor
     */
    public function startup(stubUriRequest $uriRequest)
    {
        return $this;
    }

    /**
     * returns the required role of the user to be able to process the request
     *
     * @param   string  $defaultRole  a default role to return if no special role is required
     * @return  string
     */
    public function getRequiredRole($defaultRole)
    {
        return $defaultRole;
    }

    /**
     * checks whether the current request forces ssl or not
     *
     * @return  bool
     */
    public function forceSsl()
    {
        return false;
    }

    /**
     * checks whether the request is ssl or not
     *
     * @return  bool
     */
    public function isSsl()
    {
        if (null === $this->ssl) {
            $this->ssl = false;
            if ($this->request->validateHeader('SERVER_PORT')->isOneOf(array(443, '443')) === true) {
                $this->ssl = true;
            }
        }
        
        return $this->ssl;
    }

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        return false;
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return array();
    }

    /**
     * operations to be done after the request was processed
     *
     * @return  stubProcessor
     */
    public function cleanup()
    {
        return $this;
    }
}
?>