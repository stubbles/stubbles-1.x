<?php
/**
 * Class for access to request data.
 * 
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubAbstractRequest.php 2683 2010-08-24 19:33:16Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::request::stubDefaultRequestValueErrorCollection'
);
/**
 * Class for access to request data.
 * 
 * This class offers a basic implementation for the stubRequest interface
 * from which any specialized request classes can be inherited.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 */
abstract class stubAbstractRequest extends stubBaseObject implements stubRequest
{
    /**
     * filter factory to create filters with
     *
     * @var  stubFilterFactory
     */
    protected $filterFactory;
    /**
     * list of unfiltered request variables
     *
     * @var  array<string,string>
     */
    protected $unsecureParams  = array();
    /**
     * list of unfiltered header data
     *
     * @var  array<string,string>
     */
    protected $unsecureHeaders = array();
    /**
     * list of unfiltered cookie data
     *
     * @var  array<string,string>
     */
    protected $unsecureCookies = array();
    /**
     * list of errors that occurred while applying a filter on a param value
     * 
     * @var  stubRequestValueErrorCollection
     */
    private $paramErrors       = null;
    /**
     * list of errors that occurred while applying a filter on a header value
     * 
     * @var  stubRequestValueErrorCollection
     */
    private $headerErrors      = null;
    /**
     * list of errors that occurred while applying a filter on a cookie value
     * 
     * @var  stubRequestValueErrorCollection
     */
    private $cookieErrors      = null;
    /**
     * list of errors that occurred while applying a filter on the request body
     *
     * @var  stubRequestValueErrorCollection
     */
    private $bodyErrors        = null;
    /**
     * switch whether request has been cancelled or not
     *
     * @var  bool
     */
    protected $isCancelled     = false;

    /**
     * constructor
     *
     * @param  stubFilterFactory  $filterFactory  filter factory to create filters with
     */
    public final function __construct(stubFilterFactory $filterFactory)
    {
        $this->filterFactory = $filterFactory;
        $this->doConstuct();
    }

    /**
     * template method for child classes to do the real construction
     */
    protected abstract function doConstuct();

    /**
     * cloning is forbidden
     *
     * @throws  stubRuntimeException
     */
    public final function __clone()
    {
        throw new stubRuntimeException('Cloning of request is not allowed!');
    }

    /**
     * checks if requestor accepts cookies
     *
     * Warning! Detection is based on the amount of cookie values returned by
     * the user agent. If the user agent did not send any cookies this does not
     * necessarily mean that the user agent will not accept cookies.
     *
     * @return  bool
     */
    public function acceptsCookies()
    {
        return (count($this->unsecureCookies) > 0);
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
        return isset($this->unsecureParams[$paramName]);
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
        return isset($this->unsecureHeaders[$headerName]);
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
        return isset($this->unsecureCookies[$cookieName]);
    }

    /**
     * returns error collection for request parameters
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function paramErrors()
    {
        if (null === $this->paramErrors) {
            $this->paramErrors = new stubDefaultRequestValueErrorCollection();
        }

        return $this->paramErrors;
    }

    /**
     * returns error collection for request headers
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function headerErrors()
    {
        if (null === $this->headerErrors) {
            $this->headerErrors = new stubDefaultRequestValueErrorCollection();
        }

        return $this->headerErrors;
    }

    /**
     * returns error collection for request cookies
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function cookieErrors()
    {
        if (null === $this->cookieErrors) {
            $this->cookieErrors = new stubDefaultRequestValueErrorCollection();
        }

        return $this->cookieErrors;
    }

    /**
     * returns error collection for request body
     *
     * @return  stubRequestValueErrorCollection
     * @since   1.3.0
     */
    public function bodyErrors()
    {
        if (null === $this->bodyErrors) {
            $this->bodyErrors = new stubDefaultRequestValueErrorCollection();
        }

        return $this->bodyErrors;
    }

    /**
     * cancels the request, e.g. if it was detected that it is invalid
     */
    public function cancel()
    {
        $this->isCancelled = true;
    }

    /**
     * checks whether the request has been cancelled or not
     *
     * @return  bool
     */
    public function isCancelled()
    {
        return $this->isCancelled;
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
        return new stubValidatingRequestValue($paramName,
                                              $this->getValue($paramName, stubRequest::SOURCE_PARAM)
               );
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
        return new stubValidatingRequestValue($headerName,
                                              $this->getValue($headerName, stubRequest::SOURCE_HEADER)
               );
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
        return new stubValidatingRequestValue($cookieName,
                                              $this->getValue($cookieName, stubRequest::SOURCE_COOKIE)
               );
    }

    /**
     * checks whether a request body is valid or not
     *
     * @return  stubValidatingRequestValue
     * @since   1.3.0
     */
    public function validateBody()
    {
        return new stubValidatingRequestValue('body',
                                              $this->getRawData()
               );
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
        return new stubFilteringRequestValue($this->paramErrors(),
                                             $this->filterFactory,
                                             $paramName,
                                             $this->getValue($paramName, stubRequest::SOURCE_PARAM)
               );
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
        return new stubFilteringRequestValue($this->headerErrors(),
                                             $this->filterFactory,
                                             $headerName,
                                             $this->getValue($headerName, stubRequest::SOURCE_HEADER)
               );
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
        return new stubFilteringRequestValue($this->cookieErrors(),
                                             $this->filterFactory,
                                             $cookieName,
                                             $this->getValue($cookieName, stubRequest::SOURCE_COOKIE)
               );
    }

    /**
     * returns request body for filtering or validation
     *
     * @return  stubFilteringRequestValue
     * @since   1.3.0
     */
    public function readBody()
    {
        return new stubFilteringRequestValue($this->bodyErrors(),
                                             $this->filterFactory,
                                             'body',
                                             $this->getRawData()
               );
    }

    /**
     * return an array of all param names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getParamNames()
    {
        return array_keys($this->unsecureParams);
    }

    /**
     * return an array of all header names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getHeaderNames()
    {
        return array_keys($this->unsecureHeaders);
    }

    /**
     * return an array of all cookie names registered in this request
     *
     * @return  array<string>
     * @since   1.3.0
     */
    public function getCookieNames()
    {
        return array_keys($this->unsecureCookies);
    }

    /**
     * returns the raw data
     *
     * @return  string
     */
    protected abstract function getRawData();

    /**
     * returns single value with given name from requested source
     *
     * If the given value name does not exist the return value will be null.
     *
     * @param   string  $valueName
     * @param   int     $source
     * @return  string
     * @since   1.3.0
     */
    protected function getValue($valueName, $source)
    {
        $data = $this->getValues($source);
        if (isset($data[$valueName]) === false) {
            return null;
        }

        return $data[$valueName];
    }

    /**
     * returns the array with data from requested source
     *
     * @param   int  $source  source type: cookie, header, param
     * @return  array<string,string>
     */
    protected function getValues($source)
    {
        switch ($source) {
            case stubRequest::SOURCE_PARAM:
                return $this->unsecureParams;
                
            case stubRequest::SOURCE_COOKIE:
                return $this->unsecureCookies;
                
            case stubRequest::SOURCE_HEADER:
                return $this->unsecureHeaders;
            
            default:
                return $this->unsecureParams;
        }
    }
}
?>