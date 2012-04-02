<?php
/**
 * Processor for ReST interfaces.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @version     $Id: stubRestProcessor.php 3204 2011-11-02 16:12:02Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAnnotationBasedFilterFactory',
                      'net::stubbles::peer::http::stubAcceptHeader',
                      'net::stubbles::service::rest::stubFormatFactory',
                      'net::stubbles::service::rest::stubRestHandlerException',
                      'net::stubbles::service::rest::stubRestHandlerFactory',
                      'net::stubbles::service::rest::stubRestMethodsMatcher',
                      'net::stubbles::webapp::processor::stubAbstractProcessor',
                      'net::stubbles::webapp::processor::stubProcessorException'
);
/**
 * Processor for ReST interfaces.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @since       1.1.0
 */
class stubRestProcessor extends stubAbstractProcessor
{
    /**
     * factory to create formatters with
     *
     * @var  stubFormatFactory
     */
    protected $formatFactory;
    /**
     * factory to create responsible rest handler with
     *
     * @var  stubRestHandlerFactory
     */
    protected $restHandlerFactory;
    /**
     * factory to create filter instances based on annotations
     *
     * @var  stubAnnotationBasedFilterFactory
     */
    protected $annotationBasedFilterFactory;
    /**
     * name of rest handler to be executed
     *
     * @var  string
     */
    protected $routeName;
    /**
     * rest handler instance
     *
     * @var  stubObject
     */
    protected $restHandler;

    /**
     * constructor
     *
     * @param   stubRequest                       $request                       current request
     * @param   stubSession                       $session                       current session
     * @param   stubResponse                      $response                      current response
     * @param   stubFormatFactory                 $formatFactory                 factory to create formatters with
     * @param   stubRestHandlerFactory            $restHandlerFactory
     * @param   stubAnnotationBasedFilterFactory  $annotationBasedFilterFactory  factory to create filter instances based on annotations
     * @throws  stubProcessorException
     * @Inject
     */
    public function __construct(stubRequest $request,
                                stubSession $session,
                                stubResponse $response,
                                stubFormatFactory $formatFactory,
                                stubRestHandlerFactory $restHandlerFactory,
                                stubAnnotationBasedFilterFactory $annotationBasedFilterFactory)
    {
        parent::__construct($request, $session, $response);
        $this->formatFactory                = $formatFactory;
        $this->restHandlerFactory           = $restHandlerFactory;
        $this->annotationBasedFilterFactory = $annotationBasedFilterFactory;
    }

    /**
     * operations to be done before the request is processed
     *
     * @param   stubUriRequest  $uriRequest
     * @return  stubProcessor
     * @throws  stubProcessorException
     */
    public function startup(stubUriRequest $uriRequest)
    {
        $this->routeName   = $uriRequest->getRemainingUri();
        $this->restHandler = $this->restHandlerFactory->createHandler($this->routeName);
        if (null === $this->restHandler) {
            $this->response->write($this->getErrorFormatter()->formatNotFoundError());
            throw new stubProcessorException(404, 'Not Found');
        }
        
        $this->session->putValue('net.stubbles.webapp.lastPage', $this->request->getMethod() . ':' . $this->routeName);
        return $this;
    }

    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * processes the request
     *
     * @return  stubProcessor
     * @throws  stubProcessorException
     */
    public function process()
    {
        $dispatch = $this->restHandlerFactory->getDispatchUri($this->routeName);
        $method   = $this->findMethod($dispatch);
        if (null === $method) {
            $this->response->setStatusCode(405);
            $allowedMethods = $this->listMethods();
            $this->response->addHeader('Allow', join(',', $allowedMethods));
            $this->response->write($this->getErrorFormatter()->formatMethodNotAllowedError($this->request->getMethod(), $allowedMethods));
            return $this;
        }

        try {
            $formatter = $this->getFormatter($method);
            $result    = $formatter->format($method->invokeArgs($this->restHandler,
                                                                $this->extractArguments($dispatch, $method)
                                            )
                         );
        } catch (stubRestHandlerException $rhe) {
            $this->response->setStatusCode($rhe->getStatusCode());
            $result = $this->getErrorFormatter($method)->formatInternalServerError($rhe);
        } catch (stubProcessorException $pe) {
            throw $pe;
        } catch (stubException $e) {
            $this->response->setStatusCode(500);
            $result = $this->getErrorFormatter($method)->formatInternalServerError($e);
        }

        $this->response->write($result);
        return $this;
    }

    /**
     * retrieves formatter for response of given method
     *
     * In case no suitable formatter is found it will force a response with
     * HTTP 406 Not Acceptable.
     *
     * @param   stubReflectionMethod    $method
     * @return  stubFormatter
     * @throws  stubProcessorException
     */
    protected function getFormatter(stubReflectionMethod $method)
    {
        $formatter = $this->formatFactory->createFormatter($this->getAcceptedMimeTypes(), $method);
        if (null === $formatter) {
            $this->addAcceptableHeader($this->formatFactory->getSupportedMimeTypes($method));
            throw new stubProcessorException(406, 'Not Acceptable');
        }

        return $this->setContentTypeHeader($formatter);
    }

    /**
     * retrieves error formatter for response
     *
     * In case no suitable error formatter is found it will force a response
     * with HTTP 406 Not Acceptable.
     *
     * @param  stubReflectionMethod     $method  optional
     * @return  stubErrorFormatter
     * @throws  stubProcessorException
     */
    protected function getErrorFormatter(stubReflectionMethod $method = null)
    {
        $errorFormatter = $this->formatFactory->createErrorFormatter($this->getAcceptedMimeTypes(), $method);
        if (null === $errorFormatter) {
            $this->addAcceptableHeader($this->formatFactory->getSupportedErrorMimeTypes($method));
            throw new stubProcessorException(406, 'Not Acceptable');
        }

        return $this->setContentTypeHeader($errorFormatter);
    }

    /**
     * returns list of by user agent accepted mime types
     *
     * @return  stubAcceptHeader
     */
    protected function getAcceptedMimeTypes()
    {
        if ($this->request->hasHeader('HTTP_ACCEPT') === true) {
            try {
                return stubAcceptHeader::parse($this->request->readHeader('HTTP_ACCEPT')->unsecure());
            } catch (stubIllegalArgumentException $iae) {
                // do nothing, treat this as if no Accept header was set
            }
        }

        return new stubAcceptHeader();
    }

    /**
     * adds X-Acceptable header to response
     *
     * @param  array<string>  $supportedMimeTypes
     */
    protected function addAcceptableHeader(array $supportedMimeTypes)
    {
        $supportedMimeTypes = join(',', $supportedMimeTypes);
        if ('void' !== $supportedMimeTypes) {
            $this->response->addHeader('X-Acceptable', str_replace('void,', '', $supportedMimeTypes));
        }
    }

    /**
     * helper method to set the content type header
     *
     * @param   stubFormatContentType             $formatter
     * @return  stubFormatter|stubErrorFormatter
     */
    protected function setContentTypeHeader(stubFormatContentType $formatter)
    {
        if (($formatter instanceof stubVoidFormatter) === false) {
            $this->response->addHeader('Content-type', $formatter->getContentType());
        }

        return $formatter;
    }

    /**
     * finds method to be called
     *
     * Returns null if no suitable method could be found. For comparison
     * purposes the request method strings are transformed to lower case to
     * circumvent problems because of different notations.
     * If a handler has more than one method to respond to a request method the
     * method to be executed will be selected as follows:
     * 1. Method with best path match, where best path match means the first
     *    method where its path matches the beginning of $dispatch.
     * 2. Method which has no special path defined
     *
     * @param   string                $dispatch
     * @return  stubReflectionMethod
     */
    protected function findMethod($dispatch)
    {
        $dispatchCount = strlen($dispatch);
        $requestMethod = strtolower($this->request->getMethod());
        $foundedMethod = null;
        foreach ($this->restHandler->getClass()->getMethodsByMatcher(new stubRestMethodsMatcher()) as $method) {
            /* @var  $method  stubReflectionMethod */
            $restMethodAnnotation = $method->getAnnotation('RestMethod');
            if (strtolower($restMethodAnnotation->getRequestMethod()) !== $requestMethod) {
                continue;
            }

            if (0 === $dispatchCount || $restMethodAnnotation->hasPath() === false) {
                $foundedMethod = $method;
            } else {
                $path = $restMethodAnnotation->getPath();
                if (substr($dispatch, 0, strlen($path)) === $path) {
                    return $method;
                }
            }
        }

        return $foundedMethod;
    }

    /**
     * extracts arguments from dispatch data
     *
     * All parts in $dispatch which are not in the method request path will be
     * extracted as arguments.
     *
     * @param   string                $dispatch
     * @param   stubReflectionMethod  $method
     * @return  array<string>
     * @throws  stubRestHandlerException
     */
    protected function extractArguments($dispatch, stubReflectionMethod $method)
    {
        if (strlen($dispatch) === 0) {
            if ($method->getNumberOfRequiredParameters() > 0) {
                throw new stubRestHandlerException(400, 'Bad Request', 'A required parameter is missing.');
            }

            return array();
        }

        $restMethodAnnotation = $method->getAnnotation('RestMethod');
        $pathSeparator        = $restMethodAnnotation->getPathSeparator('/');
        $args = str_replace($restMethodAnnotation->getPath(), '', $dispatch);
        if (substr($args, 0, 1) === $pathSeparator) {
            $args = explode($pathSeparator, substr($args, 1));
        } else {
            $args = explode($pathSeparator, $args);
        }

        if ($method->getNumberOfRequiredParameters() > count($args)) {
            throw new stubRestHandlerException(400, 'Bad Request', 'A required parameter is missing.');
        }

        return $this->filterArguments($args, $method);
    }

    /**
     * filters arguments using filter annotations on method parameters
     *
     * @param   array<string>         $arguments
     * @param   stubReflectionMethod  $method
     * @return  array<mixed>
     * @since   1.3.0
     */
    protected function filterArguments(array $arguments, stubReflectionMethod $method)
    {
        $parameters   = $method->getParameters();
        $filteredArgs = array();
        foreach ($arguments as $position => $argument) {
            if (isset($parameters[$position]) === false) {
                return $filteredArgs; // more arguments than method parameters, return only required ones
            }

            if ($parameters[$position]->hasAnnotation('Filter') === false) {
                $filteredArgs[] = $argument;
            } else {
                try {
                    $filteredArgs[] = $this->annotationBasedFilterFactory
                                           ->createForAnnotation($parameters[$position]->getAnnotation('Filter'))
                                           ->execute($argument);
                } catch (stubFilterException $fe) {
                    throw new stubRestHandlerException(400,
                                                       'Bad Request',
                                                       'Error on argument ' . $parameters[$position]->getName() . ': ' . $fe->getMessage()
                    );
                }
            }
        }

        return $filteredArgs;
    }

    /**
     * creates a list of all methods available on current rest handler
     *
     * @return  array<string>
     */
    protected function listMethods()
    {
        $methods = array();
        foreach ($this->restHandler->getClass()->getMethodsByMatcher(new stubRestMethodsMatcher()) as $method) {
            /* @var  $method  stubReflectionMethod */
            $methods[] = strtoupper($method->getAnnotation('RestMethod')
                                           ->getRequestMethod()
            );
        }
        
        return array_values(array_unique($methods));
    }
}
?>