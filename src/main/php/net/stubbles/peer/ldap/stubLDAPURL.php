<?php
/**
 * Entry point for LDAP usage (via stubLDAPURL::fromString(urlString)).
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @version     $Id: stubLDAPURL.php 2352 2009-10-09 12:09:21Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::stubURL',
                      'net::stubbles::peer::ldap::stubLDAPConnection',
                      'net::stubbles::peer::ldap::stubLDAPURLContainer'
);
/**
 * Entry point for LDAP usage (via stubLDAPURL::fromString(urlString)).
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @see         RFC 4510  LDAP: Technical Specification Road Map              http://tools.ietf.org/html/rfc4510
 * @see         RFC 4514  LDAP: String Representation of Distinguished Names  http://tools.ietf.org/html/rfc4514
 * @see         RFC 4516  LDAP: Uniform Resource Locator                      http://tools.ietf.org/html/rfc4516
 * @see         RFC 4515  LDAP: String Representation of Search Filters       http://tools.ietf.org/html/rfc4515
 */
class stubLDAPURL extends stubURL implements stubLDAPURLContainer
{
    /**
     * LDAP scope constant for one
     */
    const SCOPE_ONE  = 'one';
    /**
     * LDAP scope constant for base
     */
    const SCOPE_BASE = 'base';
    /**
     * LDAP scope constant for sub
     */
    const SCOPE_SUB  = 'sub';

    /**
     * constructor
     *
     * @param  string  $urlString
     */
    protected function __construct($urlString)
    {
        // use default host if none given
        if (preg_match('~///~', $urlString) === 1) {
            $urlString = str_replace('///', '//localhost/', $urlString);
        }

        // setting $this->url['scheme'] / ['host'] / ['port'] / ['path'] / ['query']
        $this->url = parse_url($urlString);

        // use default ports if none given
        if (isset($this->url['port']) === false) {
            $this->url['port'] = ($this->getScheme() === 'ldaps') ? 636 : 389;
        }

        // set dn
        if (isset($this->url['path']) === true) {
            $this->url['base_dn'] = ltrim($this->url['path'], '/');
        }
    }

    /**
     * Entrace point for LDAP usage.
     *
     * The urlString has to be in the following format whereas the part
     * "user:password@" is an addition from Stubbles to ease the LDAP usage:
     *
     * ldap[s]://user:password@hostname:port/base_dn?attributes?scope?filter
     *
     * port=389 ldap
     * port=636 ldaps (non standard but commmonly used)
     *
     * @param   string  $urlString
     * @return  stubLDAPURL
     * @throws  stubMalformedURLException
     */
    public static function fromString($urlString)
    {
        if (strlen($urlString) == 0) {
            return null;
        }

        $url = new self($urlString);
        if ($url->isValid() === false) {
            throw new stubMalformedURLException('The URL ' . $urlString . ' is not a valid LDAP-URL.');
        }
        
        return $url;
    }


    /**
     * Checks LDAP url validity.
     *
     * @return  boolean
     */
    public function isValid()
    {
        // check scheme, user/password & host syntax (in general)
        if (parent::isValid() === false) {
            return false;
        }

        // check scheme
        if ($this->url['scheme'] !== 'ldap' && $this->url['scheme'] !== 'ldaps') {
            return false;
        }

        // check dn
        if (isset($this->url['base_dn']) === false || preg_match('/^([A-Za-z0-9]+=[A-Za-z0-9 ]+,?)*$/', $this->url['base_dn']) === 0) {
            return false;
        }

        // (optional) attributes & (optional) filters are passed through only scope is checked
        if ($this->getParam('scope') !== null
          && (self::SCOPE_ONE !== $this->getParam('scope')
          && self::SCOPE_BASE !== $this->getParam('scope')
          && self::SCOPE_SUB !== $this->getParam('scope'))) {
            return false;
        }

        return true;
    }

    /**
     * Gets the base dn (distinguished name).
     *
     * @return  string
     */
    public function getBaseDn()
    {
        if (isset($this->url['base_dn']) === false) {
            return null;
        }

        return $this->url['base_dn'];
    }

    /**
     * Changes the originally used base dn (distinguished name).
     *
     * @param  string  $newBaseDn
     */
    public function setBaseDn($newBaseDn)
    {
        $this->url['base_dn'] = $newBaseDn;
    }

    /**
     * Gets the base dn (distinguished name).
     *
     * @return  string
     */
    public function getPath()
    {
        if (isset($this->url['base_dn']) === false) {
            return null;
        }

        return $this->url['base_dn'];
    }

    /**
     * Checks if query string was provided.
     *
     * query:= {attributes}?{scope}?{filter}'
     *
     * @return  boolean
     */
    public function hasQuery()
    {
        return ((isset($this->url['query']) === true && strlen($this->url['query']) > 0));
    }

    /**
     * Gets the value of an optional parameter from the query string.
     * Valid LDAP parameters are 'attributes', 'scope' or 'filter'.
     *
     * If they were not set before the default values are:
     * attributes = ''
     * scope      = 'base'
     * filters    = '(objectClass=*)'
     *
     * possibilities for query string:
     *   attr  scope  filter
     *    x
     *    x    ?x
     *    x    ?x     ?x
     *         ?x     ?x
     *    x    ?      ?x
     *         ?      ?x
     *
     * @param   string       $name
     * @param   string       $defaultValue
     * @return  string|null
     */
    public function getParam($name, $defaultValue = null)
    {
        $result = $defaultValue;

        if ($this->hasQuery() === false) {
            $queryValues = null;
        } else {
            $queryValues = explode('?', $this->url['query']);
        }

        switch($name) {
            case 'attributes':
                $result = ($queryValues !== null
                                && $queryValues[0] !== '') ? $queryValues[0] : '';
                break;

            case 'scope':
                $result = ($queryValues !== null
                                && count($queryValues) >= 2
                                && $queryValues[1] !== '') ? $queryValues[1] : 'base';
                break;

            case 'filter':
                $result = ($queryValues !== null
                                && count($queryValues) >= 3
                                && $queryValues[2] !== '') ? $queryValues[2] : '(objectClass=*)';
                break;

            default:
                // intentionally empty
        }

        return $result;
    }

    /**
     * Returns the LDAP url.
     *
     * @param   boolean  $withPort  optional  true if port should be within returned url string
     * @return  string
     */
    public function get($withPort = false)
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

        if ($withPort === true && isset($this->url['port']) === true) {
            $port =  ':' . $this->url['port'];
        } else {
            $port = '';
        }

        if (isset($this->url['scheme']) === true) {
            $url = $this->url['scheme'] . '://';
            if (isset($this->url['host']) === true) {
                $url .= $user . $this->url['host'] . $port;
            }

            if (isset($this->url['base_dn']) == true) {
                $url .= '/' . $this->url['base_dn'];
            }
        }

        if ($this->hasQuery() === true) {
            $possibleParams = array('attributes', 'scope', 'filter');
            foreach ($possibleParams as $val) {
                $cur = $this->getParam($val);
                if($cur !== null) {
                    $url .= '?' . $cur;
                }
            }
        }

        return $url;
    }

    /**
     * Returns a stubLDAPConnection.
     *
     * @return  stubLDAPConnection
     */
    public function connect()
    {
        return new stubLDAPConnection($this);
    }
}
?>