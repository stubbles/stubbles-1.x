<?php
/**
 * Factory to create user agent instances.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent
 * @version     $Id: stubUserAgentProvider.php 2629 2010-08-13 18:02:19Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::request::useragent::stubUserAgentFilter'
);
/**
 * Factory to create user agent instances.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent
 * @since       1.2.0
 */
class stubUserAgentProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * request instance to be used
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * filter to be used to detect the user agent
     *
     * @var  stubUserAgentFilter
     */
    protected $userAgentFilter;

    /**
     * constructor
     *
     * @param  stubRequest          $request
     * @param  stubUserAgentFilter  $userAgentFilter
     * @Inject
     */
    public function __construct(stubRequest $request, stubUserAgentFilter $userAgentFilter)
    {
        $this->request         = $request;
        $this->userAgentFilter = $userAgentFilter;
    }

    /**
     * returns the value to provide
     *
     * @param   string         $name  optional
     * @return  stubUserAgent
     */
    public function get($name = null)
    {
        return $this->request->readHeader('HTTP_USER_AGENT')->withFilter($this->userAgentFilter);
    }
}
?>