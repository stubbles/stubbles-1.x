<?php
/**
 * Processor to handle authentication and authorization on websites.
 *
 * @package     stubbles
 * @subpackage  webapp_auth
 * @version     $Id: stubAuthProcessor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::auth::stubAuthHandler',
                      'net::stubbles::webapp::processor::stubAbstractProcessorDecorator',
                      'net::stubbles::webapp::processor::stubProcessorException'
);
/**
 * Processor to handle authentication and authorization on websites.
 * 
 * @package     stubbles
 * @subpackage  webapp_auth
 */
class stubAuthProcessor extends stubAbstractProcessorDecorator
{
    /**
     * the request
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * the created response
     *
     * @var  stubResponse
     */
    protected $response;
    /**
     * authentication handler
     *
     * @var  stubAuthHandler
     */
    protected $authHandler;
    /**
     * switch whether the processor's process() method can be called
     *
     * @var  bool
     */
    protected $callProcess = false;

    /**
     * constructor
     *
     * @param  stubProcessor    $processor
     * @param  stubRequest      $request
     * @param  stubResponse     $response
     * @param  stubAuthHandler  $authHandler
     * @Inject
     */
    public function __construct(stubProcessor $processor, stubRequest $request, stubResponse $response, stubAuthHandler $authHandler)
    {
        $this->processor   = $processor;
        $this->request     = $request;
        $this->response    = $response;
        $this->authHandler = $authHandler;
    }

    /**
     * operations to be done before the request is processed
     *
     * @param   stubUriRequest  $uriRequest
     * @return  stubProcessor
     * @throws  stubProcessorException
     * @throws  stubRuntimeException
     */
    public function startup(stubUriRequest $uriRequest)
    {
        $this->processor->startup($uriRequest);
        $requiredRole = $this->processor->getRequiredRole($this->authHandler->getDefaultRole());
        if (null !== $requiredRole && $this->authHandler->userHasRole($requiredRole) === false) {
            if ($this->authHandler->hasUser() === false && $this->authHandler->requiresLogin($requiredRole) === true) {
                $this->response->addHeader('Location', $this->authHandler->getLoginUrl());
            } elseif ($this->authHandler->hasUser() === true) {
                throw new stubProcessorException(403, 'Forbidden');
            } else {
                throw new stubRuntimeException('Role is required but there is no user and the role requires no login - most likely the auth handler is errounous.');
            }
            
            $this->request->cancel();
        } else {
            $this->callProcess = true;
        }

        return $this;
    }

    /**
     * processes the request
     *
     * @return  stubProcessor
     */
    public function process()
    {
        if (true === $this->callProcess) {
            $this->processor->process();
        }

        return $this;
    }
}
?>