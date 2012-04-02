<?php
/**
 * Creates a list of all available rest services.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::lang::stubMode',
                      'net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::service::rest::index::stubRestLink',
                      'net::stubbles::service::rest::index::stubRestService',
                      'net::stubbles::service::rest::index::stubRestServices'
);
/**
 * Creates a list of all available rest services.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 * @since       1.8.0
 */
class stubIndexRestHandler extends stubBaseObject
{
    /**
     * request instance
     *
     * @var  stubRequest
     */
    private $request;
    /**
     * list of uri conditions and handlers
     *
     * @var  array<string,string>
     */
    private $handler = array();
    /**
     * current runtime mode
     *
     * @var  stubMode
     */
    private $mode;

    /**
     * constructor
     *
     * @param  stubRequest           $request
     * @param  array<string,string>  $handler  list of uri conditions and handlers
     * @Inject
     * @Named{handler}('net.stubbles.service.rest.handler')
     */
    public function __construct(stubRequest $request, array $handler)
    {
        $this->request = $request;
        $this->handler = $handler;
    }

    /**
     * sets current runtime mode (if available)
     *
     * @param  stubMode              $mode
     * @return stubIndexRestHandler
     * @Inject(optional=true)
     */
    public function setMode(stubMode $mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * list index of available routes
     *
     * @RestMethod(requestMethod='GET')
     * @return  stubRestServices
     */
    public function listServices()
    {
        $scheme   = $this->getScheme();
        $services = new stubRestServices();
        if (null !== $this->mode) {
            $services->setEnvironmentName($this->mode->name());
        }

        foreach ($this->handler as $uriCondition => $handlerClass) {
            $handlerDescription = $this->getHandlerDescription($handlerClass);
            $services->addService(new stubRestService(new stubRestLink('self', $this->getUri($scheme, $uriCondition)),
                                                      $handlerDescription['name'],
                                                      $handlerDescription['description']
                                  )
            );
        }

        return $services;
    }

    /**
     * returns current request scheme
     *
     * @return  string
     */
    private function getScheme()
    {
        if ($this->request->validateHeader('SERVER_PORT')->isOneOf(array(443, '443')) === true) {
            return 'https://';
        }

        return 'http://';
    }

    /**
     * returns description for given handler class
     *
     * @param   string                $handlerClass
     * @return  array<string,string>
     */
    private function getHandlerDescription($handlerClass)
    {
        $class = new stubReflectionClass($handlerClass);
        if ($class->hasAnnotation('RestServiceDescription')) {
            $annotation = $class->getAnnotation('RestServiceDescription');
            return array('name'        => $annotation->getName(),
                         'description' => $annotation->getDescription()
            );
        }

        return array('name' => '', 'description' => '');
    }

    /**
     * returns uri for given condition
     *
     * @param   string  $scheme
     * @param   string  $uriCondition
     * @return  string
     */
    private function getUri($scheme, $uriCondition)
    {
        return $scheme . $this->request->getURI() . $uriCondition;
    }
}
?>