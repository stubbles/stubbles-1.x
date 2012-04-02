<?php
/**
 * Class for handling request variables with a special prefix.
 * 
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubRequestPrefixDecorator.php 2680 2010-08-23 22:02:52Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::request::stubPrefixedRequestValueErrorCollection'
);
/**
 * Class for handling request variables with a special prefix.
 * 
 * This acts as a decorator around a stubRequest instance and allows to restrict
 * access to request values starting with a prefix. Via param $sources from the
 * constructor it is controlled for which source the prefix should be applied. As
 * it is a bit switch you may not only use the stubRequest::SOURCE_* constansts
 * but any combination of them as well: e.g. stubRequest::SOURCE_COOKIE +
 * stubRequest::SOURCE_PARAM applies prefixes on cookies and parameters, but not
 * on headers.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 */
class stubRequestPrefixDecorator extends stubBaseObject implements stubRequest
{
    /**
     * the decorated request
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * the prefix to use
     *
     * @var  string
     */
    protected $prefix;
    /**
     * sources to apply prefix on
     * 
     * Can be any of stubRequest::SOURCE_* or a combination of them (bit value)
     *
     * @var  int
     */
    protected $sources;

    /**
     * constructor
     *
     * @param  stubRequest  $request  the request to decorate
     * @param  string       $prefix   the prefix to use
     * @param  int          $sources  optional  can be any of stubRequest::SOURCE_* or a combination of them (bit value)
     */
    public function __construct(stubRequest $request, $prefix, $sources = stubRequest::SOURCE_PARAM)
    {
        $this->request = $request;
        $this->prefix  = $prefix;
        $this->sources = $sources;
    }

    /**
     * sets the prefix to another value
     *
     * @param   string       $prefix  the prefix to use
     * @return  stubRequest
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * checks if requestor accepts cookies
     *
     * @return  bool
     */
    public function acceptsCookies()
    {
        return $this->request->acceptsCookies();
    }

    /**
     * checks whether a request param is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasParam($paramName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_PARAM) == true) {
            $paramName = $this->prefix . '_' . $paramName;
        }

        return $this->request->hasParam($paramName);
    }

    /**
     * checks whether a request header is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasHeader($headerName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_HEADER) == true) {
            $headerName = $this->prefix . '_' . $headerName;
        }

        return $this->request->hasHeader($headerName);
    }

    /**
     * checks whether a request cookie is set
     *
     * @param   string  $paramName
     * @return  bool
     * @since   1.3.0
     */
    public function hasCookie($cookieName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_COOKIE) == true) {
            $cookieName = $this->prefix . '_' . $cookieName;
        }

        return $this->request->hasCookie($cookieName);
    }

    /**
     * returns error collection for request parameters
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function paramErrors()
    {
        $requestValueErrorCollection = $this->request->paramErrors();
        if ($this->applyPrefix(stubRequest::SOURCE_PARAM) == true) {
            return new stubPrefixedRequestValueErrorCollection($requestValueErrorCollection, $this->prefix);
        }

        return $requestValueErrorCollection;
    }

    /**
     * returns error collection for request headers
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function headerErrors()
    {
        $requestValueErrorCollection = $this->request->headerErrors();
        if ($this->applyPrefix(stubRequest::SOURCE_HEADER) == true) {
            return new stubPrefixedRequestValueErrorCollection($requestValueErrorCollection, $this->prefix);
        }

        return $requestValueErrorCollection;
    }

    /**
     * returns error collection for request cookies
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function cookieErrors()
    {
        $requestValueErrorCollection = $this->request->cookieErrors();
        if ($this->applyPrefix(stubRequest::SOURCE_COOKIE) == true) {
            return new stubPrefixedRequestValueErrorCollection($requestValueErrorCollection, $this->prefix);
        }

        return $requestValueErrorCollection;
    }

    /**
     * returns error collection for request body
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function bodyErrors()
    {
        return $this->request->bodyErrors();
    }

    /**
     * cancels the request, e.g. if it was detected that it is invalid
     * 
     * @param  stubEventDispatcher  $dispatcher  optional  dispatcher to use for signalling
     *                                                     the event, if none given the
     *                                                     default one will be used
     */
    public function cancel(stubEventDispatcher $dispatcher = null)
    {
        $this->request->cancel($dispatcher);
    }

    /**
     * checks whether the request has been cancelled or not
     *
     * @return  bool
     */
    public function isCancelled()
    {
        return $this->request->isCancelled();
    }

    /**
     * returns the request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * returns the uri of the request
     * 
     * @return  string
     */
    public function getURI()
    {
        return $this->request->getURI();
    }

    /**
     * returns complete uri including scheme
     *
     * @return  string
     * @since   1.3.0
     */
    public function getCompleteUri()
    {
        return $this->request->getCompleteUri();
    }

    /**
     * checks whether a request value from parameters is valid or not
     *
     * @param   string                      $paramName  name of request value
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateParam($paramName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_PARAM) == true) {
            $paramName = $this->prefix . '_' . $paramName;
        }

        return $this->request->validateParam($paramName);
    }

    /**
     * checks whether a request value from headers is valid or not
     *
     * @param   string                      $headerName  name of header
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateHeader($headerName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_HEADER) == true) {
            $headerName = $this->prefix . '_' . $headerName;
        }

        return $this->request->validateHeader($headerName);
    }

    /**
     * checks whether a request value from cookie is valid or not
     *
     * @param   string                      $cookieName  name of cookie
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateCookie($cookieName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_COOKIE) == true) {
            $cookieName = $this->prefix . '_' . $cookieName;
        }

        return $this->request->validateCookie($cookieName);
    }

    /**
     * checks whether a request body is valid or not
     *
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateBody()
    {
        return $this->request->validateBody();
    }

    /**
     * returns request value from params for filtering or validation
     *
     * @param   string                     $paramName  name of request value
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readParam($paramName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_PARAM) == true) {
            $paramName = $this->prefix . '_' . $paramName;
        }

        return $this->request->readParam($paramName);
    }

    /**
     * returns request value from headers for filtering or validation
     *
     * @param   string                     $headerName  name of header
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readHeader($headerName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_HEADER) == true) {
            $headerName = $this->prefix . '_' . $headerName;
        }

        return $this->request->readHeader($headerName);
    }

    /**
     * returns request value from cookies for filtering or validation
     *
     * @param   string                     $cookieName  name of cookie
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readCookie($cookieName)
    {
        if ($this->applyPrefix(stubRequest::SOURCE_COOKIE) == true) {
            $cookieName = $this->prefix . '_' . $cookieName;
        }

        return $this->request->readCookie($cookieName);
    }

    /**
     * returns request body for filtering or validation
     *
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readBody()
    {
        return $this->request->readBody();
    }

    /**
     * return an array of all param names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getParamNames()
    {
        $paramNames = $this->request->getParamNames();
        if ($this->applyPrefix(stubRequest::SOURCE_PARAM) == false) {
            return $paramNames;
        }

        return $this->filterNames($paramNames);
    }

    /**
     * return an array of all header names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getHeaderNames()
    {
        $headerNames = $this->request->getHeaderNames();
        if ($this->applyPrefix(stubRequest::SOURCE_HEADER) == false) {
            return $headerNames;
        }

        return $this->filterNames($headerNames);
    }

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getCookieNames()
    {
        $cookieNames = $this->request->getCookieNames();
        if ($this->applyPrefix(stubRequest::SOURCE_COOKIE) == false) {
            return $cookieNames;
        }

        return $this->filterNames($cookieNames);
    }

    /**
     * check whether the prefix has to be applied for requested source
     *
     * @param   int   $source  can be any of stubRequest::SOURCE_* or a combination of them (bit value)
     * @return  bool
     */
    protected function applyPrefix($source)
    {
        return (($this->sources & $source) != 0);
    }

    /**
     * filter names according to prefix
     *
     * @param   array<string>  $valueNames
     * @return  array<string>
     */
    protected function filterNames(array $valueNames)
    {
        $returnedValueNames = array();
        $checkLength        = strlen($this->prefix) + 1;
        foreach ($valueNames as $valueName) {
            if (substr($valueName, 0, $checkLength) == $this->prefix . '_') {
                $returnedValueNames[] = substr($valueName, $checkLength);
            }
        }

        return $returnedValueNames;
    }
}
?>