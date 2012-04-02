<?php
/**
 * Factory to create rest handler instances with.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @version     $Id: stubRestHandlerFactory.php 3204 2011-11-02 16:12:02Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector');
/**
 * Factory to create rest handler instances with.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @since       1.7.0
 */
class stubRestHandlerFactory extends stubBaseObject
{
    /**
     * injector instance to create rest handler instances
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * list of uri conditions and handlers
     *
     * @var  array<string,string>
     */
    protected $handler            = array();
    /**
     * condition which lead to selection of handler
     *
     * @var  string
     */
    protected $handlerUriCondition = "^/";
    /**
     * called uri
     *
     * @var  string
     */
    protected $currentUri;

    /**
     * constructor
     *
     * @param  stubInjector          $injector  injector instance to create rest handler instances with
     * @param  array<string,string>  $handler   list of uri conditions and handlers
     * @Inject
     * @Named{handler}('net.stubbles.service.rest.handler')
     */
    public function __construct(stubInjector $injector, array $handler)
    {
        $this->injector = $injector;
        $this->handler  = $handler;
    }

    /**
     * creates handler instance based on called uri
     *
     * If called uri does not satisfy any of the uri conditions the return value
     * will be null.
     *
     * @param   string        $uri  called uri
     * @return  stubObject
     */
    public function createHandler($uri)
    {
        foreach (array_keys($this->handler) as $uriCondition) {
            if (preg_match('~' . $uriCondition . '~', $uri) === 1) {
                $this->handlerUriCondition = $uriCondition;
                return $this->injector->getInstance($this->handler[$uriCondition]);
            }
        }

        return null;
    }

    /**
     * returns dispatch uri which was not part of decision for selecting the handler
     *
     * @param   string  $uri  called uri
     * @return  string
     */
    public function getDispatchUri($uri)
    {
        $matches = array();
        preg_match('~(' . $this->handlerUriCondition . ')([^?]*)?~', $uri, $matches);
        if (isset($matches[2]) === true && empty($matches[2]) === false) {
            return $matches[2];
        }

        return null;
    }
}
?>