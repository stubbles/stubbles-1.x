<?php
/**
 * Class for URLs and methods on URLs.
 *
 * @package     stubbles
 * @subpackage  peer
 * @version     $Id: stubURL.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::peer::stubMalformedURLException',
                      'net::stubbles::peer::stubURLContainer'
);
/**
 * Class for URLs and methods on URLs.
 *
 * @package     stubbles
 * @subpackage  peer
 */
class stubURL extends stubBaseObject implements stubURLContainer
{
    /**
     * internal representation after parse_url()
     *
     * @var  array
     */
    protected $url    = array();
    /**
     * parameters for url
     *
     * @var  array
     */
    protected $params = array();

    /**
     * constructor
     *
     * @param  string  $url
     */
    protected function __construct($url)
    {
        $this->url = parse_url($url);
        if (isset($this->url['host']) === true) {
            $this->url['host'] = strtolower($this->url['host']);
        }

        // bugfix for a PHP issue: ftp://user:@auxiliary.kl-s.com/
        // will lead to an unset $this->url['pass'] which is wrong
        // due to RFC1738 3.1, it has to be an empty string
        if (isset($this->url['user']) === true && isset($this->url['pass']) === false && $this->get(true) !== $url) {
            $this->url['pass'] = '';
        }

        if ($this->hasQuery() === true) {
            // do not use parse_str() as this breaks param names containing
            // dots or spaces
            foreach (explode('&', $this->url['query']) as $param) {
                if (strstr($param, '=') !== false) {
                    list($key, $value)  = explode('=', $param);
                    $this->params[$key] = $value;
                } else {
                    $this->params[$param] = '';
                }
            }
        }
    }

    /**
     * parses an url out of a string
     *
     * @param   string   $urlString
     * @return  stubURL
     * @throws  stubMalformedURLException
     */
    public static function fromString($urlString)
    {
        if (strlen($urlString) === 0) {
            return null;
        }

        $url = new self($urlString);
        if ($url->isValid() === false) {
            throw new stubMalformedURLException('The URL ' . $urlString . ' is not a valid URL.');
        }

        return $url;
    }

    /**
     * Checks whether URL is a correct URL.
     *
     * @return  bool
     */
    public function isValid()
    {
        if (strlen($this->get()) === 0) {
            return false;
        }

        if (isset($this->url['scheme']) === false) {
            return false;
        }

        if (preg_match('!^([a-z][a-z0-9\+]*)://([^@]+@)?([^/?#]*)(/([^#?]*))?(.*)$!', $this->get()) == 0) {
            return false;
        }

        if (isset($this->url['user']) === true) {
            if (preg_match('~([@:/])~', $this->url['user']) != 0) {
                return false;
            }

            if (isset($this->url['pass']) === true && preg_match('~([@:/])~', $this->url['pass']) != 0) {
                return false;
            }
        }

        if (isset($this->url['host']) === true
          && preg_match('!^([a-zA-Z0-9\.-]+|\[[^\]]+\])(:([0-9]+))?$!', $this->url['host']) != 0) {
            return true;
        } elseif (isset($this->url['host']) === false || strlen($this->url['host']) === 0) {
            return true;
        }

        return false;
    }

    /**
     * checks whether host of url is listed in dns
     *
     * @return  bool
     */
    public function checkDNS()
    {
        // no valid url, no dns :)
        if ($this->isValid() === false) {
            return false;
        }

        // no host, no dns :)
        if (isset($this->url['host']) === false) {
            return false;
        }

        if ('localhost' === $this->url['host'] || '127.0.0.1' === $this->url['host'] || '[::1]' === $this->url['host']) {
            return true;
        }

        // windows does not support dns functions :(
        if (function_exists('checkdnsrr') === false) {
            return true;
        }

        if (checkdnsrr($this->url['host'], 'ANY') === true || checkdnsrr($this->url['host'], 'MX') === true) {
            return true;
        }

        return false;
    }

    /**
     * checks whether the url uses a default port or not
     *
     * @return  bool
     */
    public function hasDefaultPort()
    {
        return false;
    }

    /**
     * returns the url
     *
     * The both port params have the following influence:
     * 1. $port = false: port gets never added to returned url, even if initially set
     * 2. $port = true, $onlyNonDefaultPort = false: port gets always added to the returned url
     * 3. $port = true, $onlyNonDefaultPort = true: port gets only added to the returned url if it is not the default port of the scheme
     *
     * @param   bool    $port                optional  true if port should be within returned url string
     * @param   bool    $onlyNonDefaultPort  optional  true if port should only be returned if it is not the default port
     * @return  string
     */
    public function get($port = false, $onlyNonDefaultPort = false)
    {
        $url  = '';
        $user = '';
        if (isset($this->url['user']) === true) {
            $user = $this->url['user'];
            if (isset($this->url['pass']) === true) {
                $user .= ':' . $this->url['pass'];
            }

            $user .= '@';
        }

        // see description about ports above about the sense of this if-construct
        if (true === $port && isset($this->url['port']) === true
          && ((true === $onlyNonDefaultPort && $this->hasDefaultPort() === false) || false === $onlyNonDefaultPort)) {
            $port =  ':' . $this->url['port'];
        } else {
            $port = '';
        }

        if (isset($this->url['scheme']) === true) {
            $url = $this->url['scheme'] . '://';
            if (isset($this->url['host']) === true) {
                $url .= $user . $this->url['host'] . $port;
            }

            if (isset($this->url['path']) === true) {
                $url .= $this->url['path'];
            }
        }

        if ($this->hasQuery() === true) {
            $url .= '?' . $this->buildQuery();
        }

        if (isset($this->url['fragment']) && strlen($this->url['fragment']) > 0) {
            $url .= '#' . $this->url['fragment'];
        }

        return $url;
    }

    /**
     * set the protocol sheme
     *
     * @param  string  $scheme  e.g. - http, https, ftp
     */
    public function setScheme($scheme)
    {
        $this->url['scheme'] = $scheme;
    }

    /**
     * returns the scheme of the url
     *
     * @return  string
     */
    public function getScheme()
    {
        if (isset($this->url['scheme']) === true) {
            return $this->url['scheme'];
        }

        return null;
    }

    /**
     * returns the user
     *
     * @param   string  $defaultUser  optional  user to return if no user is set
     * @return  string
     */
    public function getUser($defaultUser = null)
    {
        if (isset($this->url['user']) === true) {
            return $this->url['user'];
        }

        return $defaultUser;
    }

    /**
     * returns the password
     *
     * @param   string  $defaultPassword  optional  password to return if no password is set
     * @return  string
     */
    public function getPassword($defaultPassword = null)
    {
        if (isset($this->url['pass']) === true) {
            return $this->url['pass'];
        }

        return $defaultPassword;
    }

    /**
     * returns hostname of the url
     *
     * @param   string  $defaultHost  optional  default host to return if no host is defined
     * @return  string
     */
    public function getHost($defaultHost = null)
    {
        if (isset($this->url['host']) === true) {
            return $this->url['host'];
        }

        return $defaultHost;
    }

    /**
     * sets the port
     *
     * @param  int  $port
     */
    public function setPort($port)
    {
        $this->url['port'] = $port;
    }

    /**
     * returns port of the url
     *
     * @param   int     $defaultPort  optional  port to be used if no port is defined
     * @return  string
     */
    public function getPort($defaultPort = null)
    {
        if (isset($this->url['port']) === true) {
            return $this->url['port'];
        }

        return $defaultPort;
    }

    /**
     * returns path of the url
     *
     * @return  string
     */
    public function getPath()
    {
        if (isset($this->url['path']) === false) {
            return null;
        }

        if ($this->hasQuery() === true) {
            return $this->url['path'] . '?' . $this->buildQuery();
        }

        return $this->url['path'];
    }

    /**
     * checks whether url has a query
     *
     * @return  bool
     */
    public function hasQuery()
    {
        return (count($this->params) > 0 ||  (isset($this->url['query']) === true && strlen($this->url['query']) > 0));
    }

    /**
     * add a parameter to the url
     *
     * If given value is null the param will be removed.
     *
     * @param   string   $key    name of parameter
     * @param   mixed    $value  value of parameter
     * @return  stubURL
     * @throws  stubIllegalArgumentException
     */
    public function addParam($key, $value)
    {
        if (is_string($key) === false) {
            throw new stubIllegalArgumentException('Argument 1 passed to ' . __METHOD__ . '() must be an instance of string.');
        }

        if (null !== $value && is_array($value) === false && is_scalar($value) === false) {
            throw new stubIllegalArgumentException('Argument 2 passed to ' . __METHOD__ . '() must be an instance of string, array or any other scalar value or null.');
        }

        if (null === $value and isset($this->params[$key]) === true) {
            unset($this->params[$key]);
        } elseif (null !== $value) {
            if (false === $value) {
                $value = 0;
            } elseif (true === $value) {
                $value = 1;
            }

            $this->params[$key] = $value;
        }

        return $this;
    }

    /**
     * remove a param from url
     *
     * @param   string   $key    name of parameter
     * @return  stubURL
     * @since   1.1.2
     */
    public function removeParam($key)
    {
        if (isset($this->params[$key]) === true) {
            unset($this->params[$key]);
            if (count($this->params) === 0) {
                unset($this->url['query']);
            }
        }

        return $this;
    }

    /**
     * checks whether a certain param is set
     *
     * @param   string  $key
     * @return  bool
     * @since   1.1.2
     */
    public function hasParam($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * returns the value of a param
     *
     * @param   string  $name          name of the param
     * @param   mixed   $defaultValue  optional  default value to return if param is not set
     * @return  mixed
     */
    public function getParam($name, $defaultValue = null)
    {
        if (isset($this->params[$name]) === false) {
            return $defaultValue;
        }

        return $this->params[$name];
    }

    /**
     * build the query from given parameters
     *
     * @return  string
     */
    protected function buildQuery()
    {
        if ($this->hasQuery() === false) {
            return null;
        }

        $query = '';
        foreach ($this->params as $key => $value) {
            if (is_array($value) === false) {
                if (strlen($query) > 0) {
                    $query .= '&';
                }

                if($value !== '') {
                    $query .= $key . '=' . urlencode($value);
                } else {
                    $query .= $key;
                }
            } else {
                foreach ($value as $assoc_key => $single) {
                    if (strlen($query) > 0) {
                        $query .= '&';
                    }

                    if (is_string($assoc_key) === true) {
                        $query .= $key . '[' . $assoc_key . ']=' . urlencode($single);
                    } else {
                        $query .= $key . '[]=' . urlencode($single);
                    }
                }
            }
        }

        return $query;
    }
}
?>
