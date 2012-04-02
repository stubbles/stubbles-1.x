<?php
/**
 * Interface to configure which interceptors and processors should respond to which uri.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::webapp::stubUriConfiguration'
);
/**
 * Interface to configure which interceptors and processors should respond to which uri.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 */
class stubUriConfigurator extends stubBaseObject
{
    /**
     * real configuration container
     *
     * @var  stubUriConfiguration
     */
    protected $uriConfig;
    /**
     * map of processor classes
     *
     * @var  array<string,string>
     */
    protected $processorMap     = array('jsonrpc' => 'net::stubbles::service::jsonrpc::stubJsonRpcProcessor',
                                        'rest'    => 'net::stubbles::service::rest::stubRestProcessor',
                                        'xml'     => 'net::stubbles::webapp::xml::stubXmlProcessor',
                                        'rss'     => 'net::stubbles::xml::rss::stubRSSProcessor'
                                  );
    /**
     * list of rest uri conditions and handlers
     *
     * @var  array<string,string>
     */
    protected $restHandler      = array();
    /**
     * list of rss uri conditions and feeds
     *
     * @var  array<string,string>
     */
    protected $rssFeeds         = array();

    /**
     * constructor
     *
     * Given fallback is used if none of the configured processors applies.
     * The default class can be left out if one of the following names is used
     * which lead to the following processor classes:
     * xml     => net::stubbles::webapp::xml::stubXmlProcessor
     * rest    => net::stubbles::service::rest::stubRestProcessor
     * jsonrpc => net::stubbles::service::jsonrpc::stubJsonRpcProcessor
     * rss     => net::stubbles::xml::rss::stubRSSProcessor
     *
     * @param   string  $defaultName   name of fallback processor
     * @param   string  $defaultClass  class name of fallback processor
     * @throws  stubIllegalArgumentException
     */
    public function __construct($defaultName, $defaultClass = null)
    {
        if (isset($this->processorMap[$defaultName]) === false && null === $defaultClass) {
            throw new stubIllegalArgumentException('Unknown processor name ' . $defaultName . ', requires a default class.');
        }

        $this->uriConfig = new stubUriConfiguration($defaultName);
        if (null !== $defaultClass) {
            $this->processorMap[$defaultName] = $defaultClass;
        }
    }

    /**
     * static constructor, see constructor above
     *
     * @param   string               $defaultName   name of fallback processor
     * @param   string               $defaultClass  class name of fallback processor
     * @return  stubUriConfigurator
     */
    public static function create($defaultName, $defaultClass = null)
    {
        return new self($defaultName, $defaultClass);
    }

    /**
     * creates configuration with stubbles' xml processor as default
     *
     * @return  stubUriConfigurator
     */
    public static function createWithXmlProcessorAsDefault()
    {
        return new self('xml');
    }

    /**
     * creates configuration with stubbles' rest processor as default
     *
     * @return  stubUriConfigurator
     */
    public static function createWithRestProcessorAsDefault()
    {
        return new self('rest');
    }

    /**
     * returns finished configuration
     *
     * @return  stubUriConfiguration
     */
    public function getConfig()
    {
        return $this->uriConfig;
    }

    /**
     * pre intercept request with given pre interceptor
     *
     * Adding the same pre interceptor class twice will overwrite the uri
     * condition set with the first registration.
     *
     * @param   string               $preInterceptorClassName
     * @param   string               $uriCondition             optional
     * @return  stubUriConfigurator
     */
    public function preIntercept($preInterceptorClassName, $uriCondition = null)
    {
        $this->uriConfig->addPreInterceptor($preInterceptorClassName, $uriCondition);
        return $this;
    }


    /**
     * adds pre interceptor which shows last request xml to uri configuration
     *
     * @param   string               $uriCondition  optional
     * @return  stubUriConfigurator
     */
    public  function addShowLastXmlPreInterceptor($uriCondition = null)
    {
        $this->uriConfig->addPreInterceptor('net::stubbles::webapp::xml::stubShowLastXmlInterceptor', $uriCondition);
        return $this;
    }

    /**
     * adds variant selection pre interceptor to uri configuration
     *
     * @param   string               $uriCondition  optional
     * @return  stubUriConfigurator
     */
    public function addVariantsPreInterceptor($uriCondition = null)
    {
        $this->uriConfig->addPreInterceptor('net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor', $uriCondition);
        return $this;
    }

    /**
     * adds pre interceptor which switches the current variant within the session to uri configuration
     *
     * @param   string               $uriCondition  optional
     * @return  stubUriConfigurator
     */
    public function addVariantSwitchPreInterceptor($uriCondition = null)
    {
        $this->uriConfig->addPreInterceptor('net::stubbles::webapp::variantmanager::stubVariantSwitchPreInterceptor', $uriCondition);
        return $this;
    }

    /**
     * process request with given processor
     *
     * The uri condition must not be empty. If you want to configure a processor
     * which is called for all requests you should configure it as the default
     * processor.
     *
     * If you add a processor for a uri condition where the processor class is
     * already known the processor class can be left out.
     *
     * Adding a different processor class for the same processor name will
     * overwrite the previous processor class added with this name, but not the
     * previous uri condition.
     *
     * Adding a different processor for the same uri condition will overwrite
     * the first processor.
     *
     * @param   string               $processorName       shortcut for processor
     * @param   string               $uriCondition
     * @param   string               $processorClassName  optional  name of processor class
     * @return  stubUriConfigurator
     * @throws  stubIllegalArgumentException
     */
    public function process($processorName, $uriCondition, $processorClassName = null)
    {
        if (empty($uriCondition) === true) {
            throw new stubIllegalArgumentException('$uriCondition can not be empty.');
        }

        if (isset($this->processorMap[$processorName]) === false && null == $processorClassName) {
            throw new stubIllegalArgumentException('Unknown processor name ' . $processorName . ', requires a processor class.');
        }

        $this->uriConfig->addProcessorName($processorName, $uriCondition);
        if (null != $processorClassName) {
            $this->processorMap[$processorName] = $processorClassName;
        }

        return $this;
    }

    /**
     * checks if a specific processor is enabled
     *
     * @param   string  $processorName
     * @return  bool
     */
    public function isProcessorEnabled($processorName)
    {
        return $this->uriConfig->isProcessorEnabled($processorName);
    }

    /**
     * returns map of processor classes
     *
     * @return  array<string,string>
     */
    public function getProcessorMap()
    {
        return $this->processorMap;
    }

    /**
     * process requests with stubbles' xml/xsl view engine
     *
     * @return  stubUriConfigurator
     */
    public function provideXml()
    {
        $this->uriConfig->addProcessorName('xml', '^/xml/');
        return $this;
    }

    /**
     * process requests with stubbles' rest processor
     *
     * @param   string               $uriCondition  optional
     * @return  stubUriConfigurator
     */
    public function provideRest($uriCondition = '/api/')
    {
        $this->uriConfig->addProcessorName('rest', $uriCondition);
        return $this;
    }

    /**
     * adds rest handler for given uri condition
     *
     * @param   string               $uriCondition
     * @param   string               $handlerClass
     * @return  stubUriConfigurator
     */
    public function withRestHandler($uriCondition, $handlerClass)
    {
        if ($this->uriConfig->isProcessorEnabled('rest') === false) {
             $this->provideRest();
        }

        $this->restHandler[$uriCondition] = $handlerClass;
        return $this;
    }

    /**
     * returns list of rest uri conditions and handlers
     *
     * @return  array<string,string>
     */
    public function getRestHandler()
    {
        return $this->restHandler;
    }

    /**
     * process rss requests with stubbles' rss processor
     *
     * @param   string               $uriCondition  optional
     * @return  stubUriConfigurator
     */
    public function provideRss($uriCondition = '^/rss/')
    {
        $this->uriConfig->addProcessorName('rss', $uriCondition);
        return $this;
    }

    /**
     * add rss feed for given uri condition
     *
     * @param   string               $uriCondition
     * @param   string               $rssFeedClass
     * @return  stubUriConfigurator
     */
    public function withRssFeed($uriCondition, $rssFeedClass)
    {
        if ($this->uriConfig->isProcessorEnabled('rss') === false) {
             $this->provideRss();
        }

        $this->rssFeeds[$uriCondition] = $rssFeedClass;
        return $this;
    }

    /**
     * returns list of rss uri conditions and feeds
     *
     * @return  array<string,string>
     */
    public function getRssFeeds()
    {
        return $this->rssFeeds;
    }

    /**
     * process json rpc requests with stubbles' json rpc processor
     *
     * @param   string               $uriCondition  optional
     * @return  stubUriConfigurator
     */
    public function provideJsonRpc($uriCondition = '^/jsonrpc/')
    {
        $this->uriConfig->addProcessorName('jsonrpc', $uriCondition);
        return $this;
    }

    /**
     * post intercept request with given post interceptor
     *
     * Adding the same post interceptor class twice will overwrite the uri
     * condition set with the first registration.
     *
     * @param   string               $postInterceptorClassName
     * @param   string               $uriCondition              optional
     * @return  stubUriConfigurator
     */
    public function postIntercept($postInterceptorClassName, $uriCondition = null)
    {
        $this->uriConfig->addPostInterceptor($postInterceptorClassName, $uriCondition);
        return $this;
    }

    /**
     * adds etag post interceptor to uri configuration
     *
     * @param   string               $uriCondition  optional
     * @return  stubUriConfigurator
     */
    public function addEtagPostInterceptor($uriCondition = null)
    {
        $this->uriConfig->addPostInterceptor('net::stubbles::ipo::interceptors::stubETagPostInterceptor', $uriCondition);
        return $this;
    }
}
?>