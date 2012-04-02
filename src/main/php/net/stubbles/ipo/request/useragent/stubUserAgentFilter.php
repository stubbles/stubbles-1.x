<?php
/**
 * Filter to detect a user agent.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     ipo_request_useragent
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::ipo::request::useragent::stubUserAgentDetector'
);
/**
 * Filter to detect a user agent.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent
 * @since       1.2.0
 */
class stubUserAgentFilter extends stubBaseObject implements stubFilter
{
    /**
     * user agent detector to be used
     *
     * @var  stubUserAgentDetector
     */
    protected $userAgentDetector;

    /**
     * constructor
     *
     * @param  stubUserAgentDetector  $userAgentDetector
     * @Inject
     */
    public function  __construct(stubUserAgentDetector $userAgentDetector)
    {
        $this->userAgentDetector = $userAgentDetector;
    }

    /**
     * changes given value into a UserAgent value object.
     *
     * @param   string         $value  user agent value
     * @return  stubUserAgent
     */
    public function execute($value)
    {
        return $this->userAgentDetector->detect($value);
    }
}
?>