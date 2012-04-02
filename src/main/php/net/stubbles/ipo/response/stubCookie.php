<?php
/**
 * Container for cookies to be send out to the user.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 * @version     $Id: stubCookie.php 2888 2011-01-11 22:26:49Z mikey $
 */
/**
 * Container for cookies to be send out to the user.
 *
 * Cookies are used to store user-related data within the user-agent
 * e.g. to help detecting that requests are done by the same user.
 * Common applications are session cookies or low-level signon help.
 *
 * @package     stubbles
 * @subpackage  ipo_response
 * @link        http://wp.netscape.com/newsref/std/cookie_spec.html
 * @link        http://www.faqs.org/rfcs/rfc2109.html
 */
class stubCookie extends stubBaseObject
{
    /**
     * name of the cookie
     *
     * @var  string
     */
    protected $name     = '';
    /**
     * value of the cookie
     *
     * @var  string
     */
    protected $value    = '';
    /**
     * timestamp when cookie expires
     *
     * @var  int
     */
    protected $expires  = 0;
    /**
     * path for which the cookie should be available
     *
     * @var  string
     */
    protected $path     = null;
    /**
     * domain where this cookie will be available
     *
     * @var  string
     */
    protected $domain   = null;
    /**
     * switch whether cookie should only be used in secure connections
     *
     * @var  bool
     */
    protected $secure   = false;
    /**
     * switch whether cookie should only be accessible through http
     *
     * @var  bool
     */
    protected $httpOnly = false;

    /**
     * constructor
     *
     * @param  string  $name   name of the cookie
     * @param  string  $value  value of the cookie
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * creates the cookie
     *
     * @param   string      $name   name of the cookie
     * @param   string      $value  value of the cookie
     * @return  stubCookie
     */
    public static function create($name, $value)
    {
        $cookie = new self($name, $value);
        return $cookie;
    }

    /**
     * set the timestamp when the cookie will expire
     *
     * Please note that $expires must be a timestamp in the future.
     *
     * @param   int         $expires  timestamp in seconds since 1970
     * @return  stubCookie
     */
    public function expiringAt($expires)
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * sets the cookie to expire after given amount of seconds
     *
     * The method will add the current timestamp to the given amount of seconds.
     *
     * @param   int         $seconds
     * @return  stubCookie
     * @since   1.5.0
     */
    public function expiringIn($seconds)
    {
        $this->expires = time() + $seconds;
        return $this;
    }

    /**
     * set the path for which the cookie should be available
     *
     * @param   string      $path
     * @return  stubCookie
     */
    public function forPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * set the domain where this cookie will be available
     *
     * @param   string      $domain
     * @return  stubCookie
     */
    public function forDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * switch whether cookie should only be used in secure connections
     *
     * @param   bool        $secure
     * @return  stubCookie
     */
    public function withSecurity($secure)
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * switch whether cookie should only be accessible through http
     *
     * @param   bool        $httpOnly
     * @return  stubCookie
     */
    public function usingHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }

    /**
     * returns name of cookie
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * returns value of cookie
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * returns expiration timestamp of cookie
     *
     * @return  int
     */
    public function getExpiration()
    {
        return $this->expires;
    }

    /**
     * returns path of cookie
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * returns domain of cookie
     *
     * @return  string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * checks whether cookie should only be used in secure connections
     *
     * @return  bool
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * checks whether cookie should only be accessible through http
     *
     * @return  bool
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * sends the cookie
     */
    // @codeCoverageIgnoreStart
    public function send()
    {
        setcookie($this->name, $this->value, $this->expires, $this->path, $this->domain, $this->secure, $this->httpOnly);
    }
    // @codeCoverageIgnoreEnd
}
?>