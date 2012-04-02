<?php
/**
 * Module to configure the binder with default instances for request, session and response.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc
 * @version     $Id: stubIpoBindingModule.php 3299 2011-12-28 16:59:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule',
                      'net::stubbles::ipo::ioc::stubFilterTypeProvider',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse'
);
/**
 * Module to configure the binder with default instances for request, session and response.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc
 */
class stubIpoBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * provider for filter types
     *
     * @var  stubFilterTypeProvider
     */
    private $filterTypeProvider;
    /**
     * name for the session
     *
     * @var  string
     */
    protected $sessionName;
    /**
     * request class to be used
     *
     * @var  string
     */
    protected $requestClassName  = 'net::stubbles::ipo::request::stubWebRequest';
    /**
     * response class to be used
     *
     * @var  string
     */
    protected $responseClassName = 'net::stubbles::ipo::response::stubBaseResponse';
    /**
     * session class to be used
     *
     * @var  string
     */
    protected $sessionClassName  = 'net::stubbles::ipo::session::stubPHPSession';

    /**
     * constructor
     *
     * @param  string  $sessionName  optional  name for the session
     */
    public function __construct($sessionName = 'PHPSESSID')
    {
        $this->sessionName = $sessionName;
    }

    /**
     * static constructor
     *
     * @param   string                $sessionName  optional  name for the session
     * @return  stubIpoBindingModule
     * @since   1.3.0
     */
    public static function create($sessionName = 'PHPSESSID')
    {
        return new self($sessionName);
    }

    /**
     * sets class name of request class to be used
     *
     * @param   string                $requestClassName  name of request class to bind
     * @return  stubIpoBindingModule
     * @since   1.1.0
     */
    public function setRequestClassName($requestClassName)
    {
        $this->requestClassName = $requestClassName;
        return $this;
    }

    /**
     * use request implementation with modifiable parameters, headers and cookies
     *
     * @return  stubIpoBindingModule
     * @since   1.7.0
     */
    public function useModifiableRequest()
    {
        $this->requestClassName = 'net::stubbles::ipo::request::stubModifiableWebRequest';
        return $this;
    }

    /**
     * use request implementation for redirected requests
     *
     * @return  stubIpoBindingModule
     * @since   1.7.0
     */
    public function useRedirectRequest()
    {
        $this->requestClassName = 'net::stubbles::ipo::request::stubRedirectRequest';
        return $this;
    }

    /**
     * sets class name of response class to be used
     *
     * @param   string                $responseClassName  name of request class to bind
     * @return  stubIpoBindingModule
     * @since   1.1.0
     */
    public function setResponseClassName($responseClassName)
    {
        $this->responseClassName = $responseClassName;
        return $this;
    }

    /**
     * sets class name of session class to be used
     *
     * @param   string                $sessionClassName  name of session class to bind
     * @return  stubIpoBindingModule
     * @since   1.1.0
     */
    public function setSessionClassName($sessionClassName)
    {
        $this->sessionClassName = $sessionClassName;
        return $this;
    }

    /**
     * use php's default session implementation
     *
     * @return  stubIpoBindingModule
     * @since   1.7.0
     */
    public function useDefaultSession()
    {
        $this->sessionClassName = 'net::stubbles::ipo::session::stubPHPSession';
        return $this;
    }

    /**
     * use none durable session implementation
     *
     * @return  stubIpoBindingModule
     * @since   1.7.0
     */
    public function useNoneDurableSession()
    {
        $this->sessionClassName = 'net::stubbles::ipo::session::stubNoneDurableSession';
        return $this;
    }

    /**
     * use none storing session implementation
     *
     * @return  stubIpoBindingModule
     * @since   1.7.0
     */
    public function useNoneStoringSession()
    {
        $this->sessionClassName = 'net::stubbles::ipo::session::stubNoneStoringSession';
        return $this;
    }

    /**
     * adds a filter class for a given type
     *
     * @param   string                $className  full qualified class name of filter class
     * @param   string                $type       name of type the filter is added for
     * @return  stubIpoBindingModule
     * @since   1.3.0
     */
    public function addFilterForType($className, $type)
    {
        $this->createFilterTypeProvider()->addFilterForType($className, $type);
        return $this;
    }

    /**
     * adds a filter annotation reader class for given filter annotation
     *
     * @param   string                $className
     * @param   string                $annotationName
     * @return  stubIpoBindingModule
     * @since   1.6.0
     */
    public function addFilterAnnotationReader($className, $annotationName)
    {
        $this->createFilterTypeProvider()->addFilterAnnotationReader($className, $annotationName);
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bindConstant()
               ->named('net.stubbles.ipo.request.filter.types')
               ->toProvider($this->createFilterTypeProvider());
        $binder->bindConstant()
               ->named('net.stubbles.ipo.request.filter.annotationreader')
               ->toProvider($this->createFilterTypeProvider());
        $request  = $this->createRequest($binder->getInjector()->getInstance('stubFilterFactory'));
        $response = $this->createResponse($request);
        $session  = $this->createSession($request, $response);
        $binder->setSessionForSessionScope($session);
        $binder->bind('stubRequest')
               ->toInstance($request);
        $binder->bind('stubSession')
               ->toInstance($session);
        $binder->bind('stubResponse')
               ->toInstance($response);
        $binder->bindConstant()
               ->named('net.stubbles.session.name')
               ->to($this->sessionName);
    }

    /**
     * creates request instance
     *
     * @param   stubFilterFactory  $filterFactory
     * @return  stubRequest
     */
    protected function createRequest(stubFilterFactory $filterFactory)
    {
        $nqClassName = stubClassLoader::getNonQualifiedClassName($this->requestClassName);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($this->requestClassName);
        }

        return new $nqClassName($filterFactory);
    }

    /**
     * creates response instance
     *
     * @param   stubRequest   $request
     * @return  stubResponse
     */
    protected function createResponse(stubRequest $request)
    {
        $httpVersion = $request->readHeader('SERVER_PROTOCOL')->unsecure();
        $minor       = null;
        $scanResult  = sscanf($httpVersion, 'HTTP/%*[1].%[01]', $minor);
        $response    = $this->createResponseInstance('1.' . ((int) $minor));
        if (2 != $scanResult) {
            $response->setStatusCode(505);
            $response->write('Unsupported HTTP protocol version "' . $httpVersion . '", expected HTTP/1.0 or HTTP/1.1' . "\n");
            $request->cancel();
        }

        return $response;
    }

    /**
     * creates response instance
     *
     * @param   string        $version
     * @return  stubResponse
     */
    protected function createResponseInstance($version)
    {
        $nqClassName = stubClassLoader::getNonQualifiedClassName($this->responseClassName);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($this->responseClassName);
        }

        return new $nqClassName($version);
    }

    /**
     * creates session instance
     *
     * @param   stubRequest   $request
     * @param   stubResponse  $response
     * @return  stubSession
     */
    protected function createSession(stubRequest $request, stubResponse $response)
    {
        $nqClassName = stubClassLoader::getNonQualifiedClassName($this->sessionClassName);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($this->sessionClassName);
        }

        return new $nqClassName($request, $response, $this->sessionName);
    }

    /**
     * creates filter type provider
     *
     * @return  stubFilterTypeProvider
     */
    protected function createFilterTypeProvider()
    {
        if (null === $this->filterTypeProvider) {
            $this->filterTypeProvider = new stubFilterTypeProvider();
        }

        return $this->filterTypeProvider;
    }
}
?>