<?php
/**
 * Container which holds the uri configuration for the web app.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::webapp::stubUriRequest'
);
/**
 * Container which holds the uri configuration for the web app.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 */
class stubUriConfiguration extends stubBaseObject
{
    /**
     * fallback if none of the configured processors applies
     *
     * @var  string
     */
    protected $defaultProcessor;
    /**
     * list of pre interceptors with their uri condition
     *
     * @var  array<string,string>
     */
    protected $preInterceptors  = array();
    /**
     * list of processor names with their uri condition
     *
     * @var  array<string,string>
     */
    protected $processors       = array();
    /**
     * list of post interceptors with their uri condition
     *
     * @var  array<string,string>
     */
    protected $postInterceptors = array();

    /**
     * constructor
     *
     * @param  string  $defaultProcessor  fallback if none of the configured processors applies
     */
    public function __construct($defaultProcessor)
    {
        $this->defaultProcessor = $defaultProcessor;
    }

    /**
     * pre intercept request with given pre interceptor
     *
     * @param   string                $preInterceptorClassName
     * @param   string                $uriCondition             optional
     * @return  stubUriConfiguration
     */
    public function addPreInterceptor($preInterceptorClassName, $uriCondition = null)
    {
        $this->preInterceptors[$preInterceptorClassName] = $uriCondition;
        return $this;
    }

    /**
     * returns class name list of pre interceptors applicable to called uri
     *
     * @return  array<string>
     */
    public function getPreInterceptors(stubUriRequest $calledUri)
    {
        return $this->getApplicable($calledUri, $this->preInterceptors);
    }

    /**
     * process request with given processor
     *
     * @param   string                $processorName  shortcut for processor
     * @param   string                $uriCondition
     * @return  stubUriConfiguration
     * @throws  stubIllegalArgumentException
     */
    public function addProcessorName($processorName, $uriCondition)
    {
        if (empty($uriCondition) === true) {
            throw new stubIllegalArgumentException('$uriCondition can not be empty.');
        }

        $this->processors[$uriCondition] = $processorName;
        return $this;
    }

    /**
     * returns processor name applicable to called uri
     *
     * Only one processor will be applied. If there is more than one processor
     * which is configured with an url condition which satifies the called uri
     * only the first of them will be used.
     *
     * If no processor has a satisfieing uri condition the default processor will
     * be returned.
     *
     * @return  string
     */
    public function getProcessorName(stubUriRequest $calledUri)
    {
        foreach ($this->processors as $uriCondition => $processorName) {
            if ($calledUri->satisfies($uriCondition) === true) {
                $calledUri->setProcessorUriCondition($uriCondition);
                return $processorName;
            }
        }

        return $this->defaultProcessor;
    }

    /**
     * checks if a specific processor is enabled
     *
     * @param   string  $processorName
     * @return  bool
     */
    public function isProcessorEnabled($processorName)
    {
        return ($processorName === $this->defaultProcessor || in_array($processorName, $this->processors));
    }

    /**
     * pre intercept request with given post interceptor
     *
     * @param   string                $postInterceptorClassName
     * @param   string                $uriCondition              optional
     * @return  stubUriConfiguration
     */
    public function addPostInterceptor($postInterceptorClassName, $uriCondition = null)
    {
        $this->postInterceptors[$postInterceptorClassName] = $uriCondition;
        return $this;
    }

    /**
     * returns class name list of post interceptors applicable to called uri
     *
     * @param   stubUriRequest  $calledUri
     * @return  array<string>
     */
    public function getPostInterceptors(stubUriRequest $calledUri)
    {
        return $this->getApplicable($calledUri, $this->postInterceptors);
    }

    /**
     * calculates which $interceptors are applicable for called uri based on uri condition
     *
     * @param   stubUriRequest        $calledUri
     * @param   array<string,string>  $interceptors
     * @return  array<string>
     */
    protected function getApplicable(stubUriRequest $calledUri, array $interceptors)
    {
        $applicable = array();
        foreach ($interceptors as  $className => $uriCondition) {
            if ($calledUri->satisfies($uriCondition) === true) {
                $applicable[] = $className;
            }
        }

        return $applicable;
    }
}
?>