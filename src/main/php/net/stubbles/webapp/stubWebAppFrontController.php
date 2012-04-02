<?php
/**
 * Frontend controller for web applications.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::webapp::stubUriRequest',
                      'net::stubbles::webapp::stubUriConfiguration',
                      'net::stubbles::webapp::processor::stubProcessor'
);
/**
 * Frontend controller for web applications.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 */
class stubWebAppFrontController extends stubBaseObject
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
     * injector instance to createinterceptor and processor instances
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * config which interceptors and processors should respond to which uri
     *
     * @var  stubUriConfiguration
     */
    protected $uriConfig;

    /**
     * constructor
     *
     * @param  stubRequest           $request    request data container
     * @param  stubSession           $session    session container
     * @param  stubResponse          $response   response container
     * @param  stubInjector          $injector   injector instance to create interceptor and processor instances
     * @param  stubUriConfiguration  $uriConfig  config which interceptors and processors should respond to which uri
     * @Inject
     */
    public function __construct(stubRequest $request,
                                stubSession $session,
                                stubResponse $response,
                                stubInjector $injector,
                                stubUriConfiguration $uriConfig)
    {
        $this->request   = $request;
        $this->session   = $session;
        $this->response  = $response;
        $this->injector  = $injector;
        $this->uriConfig = $uriConfig;
    }

    /**
     * does the whole processing
     */
    public function process()
    {
        if ($this->request->isCancelled() === false) {
            $calledUri = new stubUriRequest($this->request->readHeader('REQUEST_URI')->unsecure());
            if ($this->applyPreInterceptors($calledUri) === true) {
                if ($this->applyProcessor($calledUri) === true) {
                    $this->applyPostInterceptors($calledUri);
                }
            }
        }

        $this->setSessionData();
        $this->response->send();
    }

    /**
     * apply configured pre interceptors to called uri
     *
     * Returns false if one of the pre interceptors cancels the request.
     *
     * @param   stubUriRequest  $calledUri
     * @return  bool
     */
    protected function applyPreInterceptors(stubUriRequest $calledUri)
    {
        foreach ($this->uriConfig->getPreInterceptors($calledUri) as $interceptorClassName) {
            $this->injector->getInstance($interceptorClassName)
                           ->preProcess($this->request, $this->session, $this->response);
            if ($this->request->isCancelled() === true) {
                return false;
            }
        }

        return true;
    }

    /**
     * apply configured processor to called uri
     *
     * Returns false if the processor cancels the request, throws an exception
     * or processor requires ssl but current request is not in ssl.
     *
     * @param   stubUriRequest  $calledUri
     * @return  bool
     */
    protected function applyProcessor(stubUriRequest $calledUri)
    {
        $processor = null;
        try {
            $processor = $this->injector->getInstance('stubProcessor', $this->uriConfig->getProcessorName($calledUri));
            $processor->startup($calledUri);
            if ($processor->forceSsl() === true && $processor->isSsl() === false) {
                $this->response->redirect('https://' . $this->request->getURI());
                $this->request->cancel();
                return false;
            }

            
            $processor->process();
        } catch (stubProcessorException $pe) {
            $this->response->setStatusCode($pe->getStatusCode());
            $this->request->cancel();
        }

        if (null !== $processor) {
            $processor->cleanup();
        }

        if ($this->request->isCancelled() === true) {
            return false;
        }

        return true;
    }

    /**
     * apply configured post interceptors to called uri
     *
     * @param  stubUriRequest  $calledUri
     */
    protected function applyPostInterceptors(stubUriRequest $calledUri)
    {
        foreach ($this->uriConfig->getPostInterceptors($calledUri) as $interceptorClassName) {
            $this->injector->getInstance($interceptorClassName)
                           ->postProcess($this->request, $this->session, $this->response);
            if ($this->request->isCancelled() === true) {
                return;
            }
        }
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