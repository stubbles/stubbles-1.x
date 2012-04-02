<?php
/**
 * Class for URLs of scheme hypertext transfer protocol.
 *
 * @package     stubbles
 * @subpackage  peer_http
 * @version     $Id: stubHTTPURL.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubURL',
                      'net::stubbles::peer::http::stubHTTPURLContainer'
);
/**
 * Class for URLs of scheme hypertext transfer protocol.
 *
 * @package     stubbles
 * @subpackage  peer_http
 */
class stubHTTPURL extends stubURL implements stubHTTPURLContainer
{
    /**
     * constructor
     *
     * @param   string  $url
     * @throws  stubMalformedURLException
     */
    protected function __construct($url)
    {
        parent::__construct($url);
        if (isset($this->url['port']) === false) {
            $this->url['port']   = ('https' == $this->getScheme()) ? 443 : 80;
        }
        
        if (isset($this->url['path']) === false || strlen($this->url['path']) === 0) {
            $this->url['path'] = '/';
        }
    }

    /**
     * parses an url out of a string
     *
     * @param   string       $urlString
     * @return  stubHTTPURL
     * @throws  stubMalformedURLException
     */
    public static function fromString($urlString)
    {
        if (strlen($urlString) === 0) {
            return null;
        }
        
        $url = new self($urlString);
        if ($url->isValid() == false) {
            throw new stubMalformedURLException('The URL ' . $urlString . ' is not a valid HTTP-URL.');
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
        if (parent::isValid() === false) {
            return false;
        }
        
        if ('http' !== $this->url['scheme'] && 'https' !==  $this->url['scheme']) {
            return false;
        }
        
        return true;
    }

    /**
     * checks whether the url uses a default port or not
     *
     * Default ports are 80 for http and 443 for https
     *
     * @return  bool
     */
    public function hasDefaultPort()
    {
        if ('http' === $this->url['scheme'] && $this->url['port'] != 80) {
            return false;
        }
        
        if ('https' === $this->url['scheme'] && $this->url['port'] != 443) {
            return false;
        }
        
        return true;
    }

    /**
     * creates a stubHTTPConnection for this URL
     *
     * To submit a complete HTTP request use this:
     * <code>
     * $response = $url->connect()->asUserAgent('Not Mozilla')
     *                            ->timeout(5)
     *                            ->usingHeader('X-Money', 'Euro')
     *                            ->get();
     * </code>
     *
     * @param   stubHeaderList      $headers  optional  list of headers to be used
     * @return  stubHTTPConnection
     */
    public function connect(stubHeaderList $headers = null)
    {
        stubClassLoader::load('net::stubbles::peer::http::stubHTTPConnection');
        return new stubHTTPConnection($this, $headers);
    }
}
?>