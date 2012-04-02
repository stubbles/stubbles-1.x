<?php
/**
 * Decorator for lazy loading of pre interceptors: load and execute a pre
 * interceptor only if a specific request param is set.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @version     $Id: stubRequestPreInterceptor.php 3249 2011-11-30 18:04:16Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubPreInterceptor');
/**
 * Decorator for lazy loading of pre interceptors: load and execute a pre
 * interceptor only if a specific request param is set.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubRequestPreInterceptor extends stubSerializableObject implements stubPreInterceptor
{
    /**
     * class name of the decorated pre interceptor
     *
     * @var  string
     */
    protected $decoratedPreInterceptor;
    /**
     * name of the request param from which the decorated pre interceptor is dependend
     *
     * @var  string
     */
    protected $requestParamName;

    /**
     * constructor
     *
     * @param  string  $decoratedPreInterceptor  class name of the decorated pre interceptor
     * @param  string  $requestParamName         name of the request param from which the decorated pre interceptor is dependend
     */
    public function __construct($decoratedPreInterceptor, $requestParamName)
    {
        $this->decoratedPreInterceptor = $decoratedPreInterceptor;
        $this->requestParamName        = $requestParamName;
        
    }

    /**
     * returns class name of the decorated pre interceptor
     *
     * @return  string
     */
    public function getDecoratedPreInterceptor()
    {
        return $this->decoratedPreInterceptor;
    }

    /**
     * returns name of the request param from which the decorated pre interceptor is dependend
     *
     * @return  string
     */
    public function getRequestParamName()
    {
        return $this->requestParamName;
    }

    /**
     * does the preprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function preProcess(stubRequest $request, stubSession $session, stubResponse $response)
    {
        if ($request->hasParam($this->requestParamName) === false) {
            return;
        }
        
        $nqClassName = stubClassLoader::getNonQualifiedClassName($this->decoratedPreInterceptor);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($this->decoratedPreInterceptor);
        }
            
        $interceptor = new $nqClassName();
        $interceptor->preProcess($request, $session, $response);
    }
}
?>