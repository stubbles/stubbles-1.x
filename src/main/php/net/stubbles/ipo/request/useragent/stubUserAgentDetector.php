<?php
/**
 * Detector which detects single properties of user agents.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent
 * @version     $Id: stubUserAgentDetector.php 2595 2010-07-27 11:17:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::useragent::stubUserAgent');
/**
 * Detector which detects single properties of user agents.
 *
 * Currently it only supports to detect if a user agent is a bot. This detection
 * is limited to the Googlebot, MSNbot, Yahoo! Slurp and the DotBot.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent
 * @since       1.2.0
 */
class stubUserAgentDetector extends stubBaseObject
{
    /**
     * list of known bot user agents
     *
     * @var  array<string>
     */
    protected $botUserAgents = array('google' => '~Googlebot~',
                                     'msnbot' => '~msnbot~',
                                     'slurp'  => '~Slurp~',
                                     'dotbot' => '~DotBot~'
                               );

    /**
     * detects the user agent
     *
     * @param   string         $userAgentString
     * @return  stubUserAgent
     */
    public function detect($userAgentString)
    {
        return new stubUserAgent($userAgentString, $this->isBot($userAgentString));
    }

    /**
     * helper method to detect whether a user agent is a bot or not
     *
     * @param   string  $userAgentString
     * @return  bool
     */
    protected function isBot($userAgentString)
    {
        foreach ($this->botUserAgents as $botUserAgent) {
            if (preg_match($botUserAgent, $userAgentString) === 1) {
                return true;
            }
        }

        return false;
    }
}
?>