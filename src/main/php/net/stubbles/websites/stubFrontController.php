<?php
/**
 * The front controller for websites.
 *
 * @package     stubbles
 * @subpackage  websites
 * @version     $Id: stubFrontController.php 3162 2011-08-12 14:25:03Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubInterceptorInitializer',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::webapp::stubDummyUriRequest',
                      'net::stubbles::websites::processors::stubProcessorException',
                      'net::stubbles::websites::processors::stubProcessorResolver'
);
/**
 * The front controller for websites.
 *
 * @package     stubbles
 * @subpackage  websites
 * @deprecated  use webapp controller instead, will be removed with 1.8.0 or 2.0.0
 */
class stubFrontController extends stubBaseObject
{
    /**
     * contains request data
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * session container
     *
     * @var  stubSession
     */
    protected $session;
    /**
     * response container
     *
     * @var  stubResponse
     */
    protected $response;
    /**
     * resolver to create the correct processor
     *
     * @var  stubProcessorResolver
     */
    protected $processorResolver;
    /**
     * resolver to create the correct processor
     *
     * @var  stubInterceptorInitializer
     */
    protected $interceptorInitializer;

    /**
     * constructor
     * 
     * @param  stubRequest                 $request                 request data container
     * @param  stubSession                 $session                 session container
     * @param  stubResponse                $response                response container
     * @param  stubProcessorResolver       $processorResolver       resolver to create the correct processor
     * @param  stubInterceptorInitializer  $interceptorInitializer  initializer to create pre- and post interceptors
     * @Inject
     */
    public function __construct(stubRequest $request, stubSession $session, stubResponse $response, stubProcessorResolver $processorResolver, stubInterceptorInitializer $interceptorInitializer)
    {
        $this->request                = $request;
        $this->session                = $session;
        $this->response               = $response;
        $this->processorResolver      = $processorResolver;
        $this->interceptorInitializer = $interceptorInitializer;
    }

    /**
     * does the whole processing
     */
    public function process()
    {
        if ($this->request->isCancelled() === true) {
            $this->setSessionData();
            $this->response->send();
            return;
        }
        
        $this->interceptorInitializer->setDescriptor($this->processorResolver->getInterceptorDescriptor($this->request))
                                     ->init();
        foreach ($this->interceptorInitializer->getPreInterceptors() as $preInterceptor) {
            $preInterceptor->preProcess($this->request, $this->session, $this->response);
            if ($this->request->isCancelled() === true) {
                $this->setSessionData();
                $this->response->send();
                return;
            }
        }
        
        $processor = null;
        try {
            $processor = $this->processorResolver->resolve($this->request, $this->session, $this->response);
            $processor->startup(new stubDummyUriRequest($this->request->readHeader('REQUEST_URI')->unsecure()));
            if ($processor->forceSsl() === true && $processor->isSsl() === false) {
                $this->response->addHeader('Location', 'https://' . $this->request->getURI());
                $this->request->cancel();
                $this->response->send();
                return;
            }
                        
            $processor->process();
        } catch (stubProcessorException $pe) {
            $this->response->setStatusCode($pe->getStatusCode());
        }
        
        if (null !== $processor) {
            $processor->cleanup();
        }
        
        if ($this->request->isCancelled() === false) {
            foreach ($this->interceptorInitializer->getPostInterceptors() as $postInterceptor) {
                $postInterceptor->postProcess($this->request, $this->session, $this->response);
                if ($this->request->isCancelled() === true) {
                    break;
                }
            }
        }
        
        $this->setSessionData();
        $this->response->send();
    }

    /**
     * helper method to replace session place holders with correct session data
     */
    protected function setSessionData()
    {
        $responseData = $this->response->getBody();
        if (strlen($responseData) > 0) {
            $contents = str_replace('$SID', $this->session->getName() . '=' . $this->session->getId(), $responseData);
            $contents = str_replace('$SESSION_NAME', $this->session->getName(), $contents);
            $this->response->replaceBody(str_replace('$SESSION_ID', $this->session->getId(), $contents));
        }
    }
}
?>