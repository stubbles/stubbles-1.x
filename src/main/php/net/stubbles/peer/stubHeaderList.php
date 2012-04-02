<?php
/**
 * Container for list of headers.
 *
 * @package     stubbles
 * @subpackage  peer
 * @version     $Id: stubHeaderList.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::peer::stubURL'
);
/**
 * Container for list of headers.
 *
 * @package     stubbles
 * @subpackage  peer
 */
class stubHeaderList extends stubBaseObject implements IteratorAggregate
{
    /**
     * list of headers
     *
     * @var  array<string,string>
     */
    private $headers = array();

    /**
     * creates headerlist from given string
     *
     * @param   string          $headers  string to parse for headers
     * @return  stubHeaderList
     */
    public static function fromString($headers)
    {
        $headerList = new self();
        $matches    = array();
        preg_match_all('=^(.+): ([^\r\n]*)=m', $headers, $matches, PREG_SET_ORDER);
        foreach ($matches as $line) {
            $headerList->put($line[1], $line[2]);
        }
        
        return $headerList;
    }

    /**
     * creates header with value for key
     *
     * @param   string          $key    name of header
     * @param   string          $value  value of header
     * @return  stubHeaderList
     * @throws  stubIllegalArgumentException
     */
    public function put($key, $value)
    {
        if (is_string($key) == false) {
            throw new stubIllegalArgumentException('Argument 1 passed to ' . __METHOD__ . ' must be an instance of string.');
        }
        
        if (is_scalar($value) == false) {
            throw new stubIllegalArgumentException('Argument 2 passed to ' . __METHOD__ . ' must be an instance of a scalar value.');
        }
        
        $this->headers[$key] = (string) $value;
        return $this;
    }

    /**
     * removes header with given key
     *
     * @param   string  $key    name of header
     * @return  stubHeaderList
     */
    public function remove($key)
    {
        if (isset($this->headers[$key]) == true) {
            unset($this->headers[$key]);
        }
        
        return $this;
    }

    /**
     * creates header for user agent
     *
     * @param   string          $userAgent  name of user agent
     * @return  stubHeaderList
     */
    public function putUserAgent($userAgent)
    {
        $this->put('User-Agent', $userAgent);
        return $this;
    }

    /**
     * creates header for referer
     *
     * @param   string          $referer  referer url
     * @return  stubHeaderList
     */
    public function putReferer($referer)
    {
        $this->put('Referer', $referer);
        return $this;
    }

    /**
     * creates header for cookie
     *
     * @param   array           $cookieValues  cookie values
     * @return  stubHeaderList
     */
    public function putCookie(array $cookieValues)
    {
        $cookieValue = '';
        foreach ($cookieValues as $key => $value) {
            $cookieValue .= $key . '=' . urlencode($value) . ';';
        }
        
        $this->put('Cookie', $cookieValue);
        return $this;
    }

    /**
     * creates header for authorization
     *
     * @param   string          $user      login name
     * @param   string          $password  login password
     * @return  stubHeaderList
     */
    public function putAuthorization($user, $password)
    {
        $this->put('Authorization', 'BASIC ' . base64_encode($user . ':' . $password));
        return $this;
    }

    /**
     * adds a date header
     *
     * @param   int             $date  optional  timestamp to use as date
     * @return  stubHeaderList
     */
    public function putDate($date = null)
    {
        if (null === $date) {
            $date = gmdate('D, d M Y H:i:s');
        } else {
            $date = gmdate('D, d M Y H:i:s', $date);
        }
        
        $this->put('Date', $date . ' GMT');
        return $this;
    }

    /**
     * creates X-Binford header
     *
     * @return  stubHeaderList
     */
    public function enablePower()
    {
        $this->put('X-Binford', 'More power!');
        return $this;
    }

    /**
     * removes all headers
     *
     * @return  stubHeaderList
     */
    public function clear()
    {
        $this->headers = array();
        return $this;
    }

    /**
     * returns value of header with given key
     *
     * @param   string  $key      name of header
     * @param   mixed   $default  optional  value to return if given header not set
     * @return  string
     */
    public function get($key, $default = null)
    {
        if ($this->containsKey($key) == true) {
            return $this->headers[$key];
        }
        
        return $default;
    }

    /**
     * returns true if an header with given key exists
     *
     * @param   string  $key  name of header
     * @return  bool
     */
    public function containsKey($key)
    {
        return isset($this->headers[$key]);
    }

    /**
     * returns an iterator object
     *
     * @return  ArrayObject
     */
    public function getIterator()
    {
        return new ArrayObject($this->headers);
    }

    /**
     * returns amount of headers
     *
     * @return  int
     */
    public function size()
    {
        return count($this->headers);
    }
}
?>