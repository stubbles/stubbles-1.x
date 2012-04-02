<?php
/**
 * Value object for user agents.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent
 * @version     $Id: stubUserAgent.php 2591 2010-07-21 15:13:19Z mikey $
 */
/**
 * Value object for user agents.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent
 * @since       1.2.0
 * @XMLTag(tagName='userAgent')
 * @ProvidedBy(net::stubbles::ipo::request::useragent::stubUserAgentProvider.class)
 */
class stubUserAgent extends stubBaseObject
{
    /**
     * name of user agent
     *
     * @var  string
     */
    protected $name;
    /**
     * whether user agent is a bot or not
     *
     * @var  bool
     */
    protected $isBot;

    /**
     * constructor
     *
     * @param  string  $name  name of user agent
     * @param  bool    $isBot whether user agent is a bot or not
     */
    public function __construct($name, $isBot)
    {
        $this->name  = $name;
        $this->isBot = $isBot;
    }

    /**
     * returns name of user agent
     *
     * @return  string
     * @XMLAttribute(attributeName='name')
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * returns whether user agent is a bot or not
     *
     * @return  bool
     * @XMLAttribute(attributeName='isBot')
     */
    public function isBot()
    {
        return $this->isBot;
    }
}
?>