<?php
/**
 * Represents a LDAP connection.
 *
 * Currently modifying of content in a LDAP directory is not supported.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @see         RFC 4510  LDAP: Technical Specification Road Map              http://tools.ietf.org/html/rfc4510
 * @see         RFC 4515  LDAP: String Representation of Search Filters       http://tools.ietf.org/html/rfc4515
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::peer::stubConnectionException',
                      'net::stubbles::peer::ldap::stubLDAPURLContainer',
                      'net::stubbles::peer::ldap::stubLDAPSearchResult'
);
/**
 * Represents a LDAP connection.
 *
 * Currently modifying of content in a LDAP directory is not supported.
 *
 * @package     stubbles
 * @subpackage  peer_ldap
 * @see         RFC 4510  LDAP: Technical Specification Road Map              http://tools.ietf.org/html/rfc4510
 * @see         RFC 4515  LDAP: String Representation of Search Filters       http://tools.ietf.org/html/rfc4515
 */
class stubLDAPConnection extends stubBaseObject
{
    /**
     * access to url parts
     *
     * @var  stubLDAPURL
     */
    protected $ldap;
    /**
     * ldap connection identifier
     *
     * @var  resource
     */
    protected $handle;
    /**
     * decision provider for protocol choice (default is LDAPv3).
     *
     * @var  bool
     */
    protected $usesProtocolVersionDefault;
    /**
     * ldap options
     *
     * Keys are used for equality check, the values are actually superfluous).
     * LDAP_OPT_NETWORK_TIMEOUT is not yet supported (requires >php5.3).
     *
     * @var  array(int, int)
     */
    protected static $options = array(LDAP_OPT_DEREF            => LDAP_OPT_DEREF,
                                      LDAP_OPT_SIZELIMIT        => LDAP_OPT_SIZELIMIT,
                                      LDAP_OPT_TIMELIMIT        => LDAP_OPT_TIMELIMIT,
                                      LDAP_OPT_PROTOCOL_VERSION => LDAP_OPT_PROTOCOL_VERSION,
                                      LDAP_OPT_ERROR_NUMBER     => LDAP_OPT_ERROR_NUMBER,
                                      LDAP_OPT_REFERRALS        => LDAP_OPT_REFERRALS,
                                      LDAP_OPT_RESTART          => LDAP_OPT_RESTART,
                                      LDAP_OPT_HOST_NAME        => LDAP_OPT_HOST_NAME,
                                      LDAP_OPT_ERROR_STRING     => LDAP_OPT_ERROR_STRING,
                                      LDAP_OPT_MATCHED_DN       => LDAP_OPT_MATCHED_DN,
                                      LDAP_OPT_SERVER_CONTROLS  => LDAP_OPT_SERVER_CONTROLS,
                                      LDAP_OPT_CLIENT_CONTROLS  => LDAP_OPT_CLIENT_CONTROLS);

    /**
     * constructor
     *
     * @param  stubLDAPURLContainer  $ldapUrl
     */
    public function __construct(stubLDAPURLContainer $ldapUrl)
    {
        $this->ldap   = $ldapUrl;
        $this->handle = ldap_connect($this->ldap->getHost(), $this->ldap->getPort());
        $this->usesProtocolVersionDefault = true;
    }

    /**
     * Checks a LDAP option.
     *
     * @param   string  $name
     * @return  bool
     * @link    http://de.php.net/manual/de/function.ldap-set-option.php
     */
    public function isOptionValid($name)
    {
        return isset(self::$options[$name]);
    }

    /**
     * Sets a LDAP option.
     *
     * Possible options and their type:
     * (depends also on which types the server returns)
     *
     * LDAP_OPT_DEREF               integer
     * LDAP_OPT_SIZELIMIT           integer
     * LDAP_OPT_TIMELIMIT           integer
     * LDAP_OPT_NETWORK_TIMEOUT     integer
     * LDAP_OPT_PROTOCOL_VERSION    integer
     * LDAP_OPT_ERROR_NUMBER        integer
     * LDAP_OPT_REFERRALS           bool
     * LDAP_OPT_RESTART             bool
     * LDAP_OPT_HOST_NAME           string
     * LDAP_OPT_ERROR_STRING        string
     * LDAP_OPT_MATCHED_DN          string
     * LDAP_OPT_SERVER_CONTROLS     array
     * LDAP_OPT_CLIENT_CONTROLS     array
     *
     * @param   string                        $name
     * @param   string                        $value
     * @throws  stubConnectionException
     * @throws  stubIllegalArgumentException
     * @return  stubLDAPConnection
     */
    public function option($name, $value)
    {
        if($this->isOptionValid($name) === false) {
            throw new stubIllegalArgumentException($name . ' is no valid LDAP option.');
        }

        if($name === LDAP_OPT_PROTOCOL_VERSION && $value != 3) {
            $this->usesProtocolVersionDefault = false;
        }

        $success = @ldap_set_option($this->handle, $name, $value);
        if($success === false) {
            throw new stubConnectionException(ldap_error($this->handle));
        }

        return $this;
    }

    /**
     * Gets a LDAP option.
     *
     * Possible options and their type:
     * (depends also on which types the server returns)
     *
     * LDAP_OPT_DEREF               integer
     * LDAP_OPT_SIZELIMIT           integer
     * LDAP_OPT_TIMELIMIT           integer
     * LDAP_OPT_NETWORK_TIMEOUT     integer
     * LDAP_OPT_PROTOCOL_VERSION    integer
     * LDAP_OPT_ERROR_NUMBER        integer
     * LDAP_OPT_REFERRALS           bool
     * LDAP_OPT_RESTART             bool
     * LDAP_OPT_HOST_NAME           string
     * LDAP_OPT_ERROR_STRING        string
     * LDAP_OPT_MATCHED_DN          string
     * LDAP_OPT_SERVER_CONTROLS     array
     * LDAP_OPT_CLIENT_CONTROLS     array
     *
     * @param   string                        $name
     * @throws  stubConnectionException
     * @throws  stubIllegalArgumentException
     * @return  mixed                         $retValue
     */
    public function getOption($name)
    {
        if($this->isOptionValid($name) === false) {
            throw new stubIllegalArgumentException($name . ' is no valid LDAP Option.');
        }

        $success = @ldap_get_option($this->handle, $name, $retValue);
        if($success === false) {
            throw new stubConnectionException(ldap_error($this->handle));
        }

        return $retValue;
    }

    /**
     * Binds the LDAP connection (authentication).
     * Uses per default LDAPv3.
     *
     * @throws  stubConnectionException
     * @return  stubLDAPConnection
     */
    public function bind()
    {
        if($this->usesProtocolVersionDefault === true) {
            $this->option(LDAP_OPT_PROTOCOL_VERSION, 3);
        }

        if($this->ldap->getUser() === null || $this->ldap->getPassword() === null) {
            // anonymous bind
            $success = @ldap_bind($this->handle);
        } else {
            $success = @ldap_bind($this->handle, $this->ldap->getUser(), $this->ldap->getPassword());
        }

        if($success === false) {
            throw new stubConnectionException(ldap_error($this->handle));
        }

        return $this;
    }

    /**
     * Unbinds (closes) the LDAP connection.
     */
    public function unbind()
    {
        if(gettype($this->handle) !== 'unknown type' ) {
            ldap_unbind($this->handle);
        }
    }

    /**
     * Changes the originally used base dn (distinguished name).
     *
     * @param  string  $newBaseDn
     */
    public function setBaseDn($newBaseDn)
    {
        $this->ldap->setBaseDn($newBaseDn);
    }

    /**
     * Retrieves the LDAP entries.
     * If no parameters set, the paramteres from the LDAP url are used (or the defaults).
     *
     * @param   string                        $attributes  ldap attributes  narrow result set
     * @param   string                        $scope       ldap scope       (base|one|sub)
     * @param   string                        $filter      ldap filter      must be surrounded with parentheses
     * @throws  stubConnectionException
     * @throws  stubIllegalArgumentException
     * @return  stubLDAPSearchResult
     */
    public function search($attributes = null, $scope = null, $filter = null)
    {
        $result = false;

        // set attributes
        if($attributes === null) {
            $attributes = $this->ldap->getParam('attributes');
            $attributes = ($attributes !== null && $attributes !== '') ? explode(',', $attributes) : array();
        } else {
            $attributes = explode(',', $attributes);
        }

        // set scope
        if($scope === null) {
            $scope = $this->ldap->getParam('scope');
        }

        // set filter
        if($filter === null) {
            $filter = $this->ldap->getParam('filter');
        }

        switch($scope) {
            case stubLDAPURL::SCOPE_BASE:
                $result = @ldap_read($this->handle, $this->ldap->getBaseDn(), $filter, $attributes);
                break;

            case stubLDAPURL::SCOPE_ONE:
                $result = @ldap_list($this->handle, $this->ldap->getBaseDn(), $filter, $attributes);
                break;

            case stubLDAPURL::SCOPE_SUB:
                $result = @ldap_search($this->handle, $this->ldap->getBaseDn(), $filter, $attributes);
                break;

            default:
                throw new stubIllegalArgumentException('Inavlid scope: ' . $scope . ', must be one of "base", "sub" or "one".');
        }

        if($result === false) {
            throw new stubConnectionException(ldap_error($this->handle));
        }

        return new stubLDAPSearchResult($this->handle, $result);
    }
}
?>