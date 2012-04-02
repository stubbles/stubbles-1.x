<?php
/**
 * Specialized class for access to web request data.
 * 
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubWebRequest.php 2616 2010-08-03 16:41:04Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubAbstractRequest');
/**
 * Specialized class for access to web request data.
 * 
 * Please be aware that GET and POST values are merged to param values. There
 * is no possibility to detect if a param value is originally from GET or POST,
 * but one may use the getMethod() method to check if the request type was GET
 * or POST (return value of getMethod() is lower case only).
 * The headers contain all stuff from $_SERVER.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 */
class stubWebRequest extends stubAbstractRequest
{
    /**
     * template method for child classes to do the real construction
     */
    protected function doConstuct()
    {
        $this->unsecureParams  = array_merge(array_map(array($this, 'stripSlashes'), $_GET), array_map(array($this, 'stripSlashes'), $_POST));
        $this->unsecureHeaders = $_SERVER;
        $this->unsecureCookies = $_COOKIE;
    }
    
    /**
     * strips slashes from request variables, even if request variable is an array
     * (wrapper around native stripslashes())
     *
     * @param   string|array  $value  request variable value to apply stripslashes on
     * @return  string|array  stripslashed' value
     */
    private function stripSlashes($value)
    {
        if (is_array($value) === false) {
            return stripslashes($value);
        }
        
        $tmp = array();
        foreach ($value as $id => $content) {
            $tmp[$id] = $this->stripslashes($content);
        }
            
        return $tmp;
    }
    
    /**
     * returns the request method
     *
     * @return  string
     */
    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
    
    /**
     * returns the uri of the request
     *
     * Return value depends on the HOST header. If the user agent does not send
     * a HOST header the URI will only consist of the REQUEST_URI.
     * 
     * @return  string
     */
    public function getURI()
    {
        if (isset($_SERVER['HTTP_HOST']) === true) {
            return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * returns complete uri including scheme
     *
     * @return  string
     * @since   1.3.0
     */
    public function getCompleteUri()
    {
        if (isset($_SERVER['SERVER_PORT']) === true && '443' == $_SERVER['SERVER_PORT']) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        return $scheme . '://' . $this->getURI();
    }
    
    /**
     * returns the raw data
     *
     * @return  string
     */
    protected function getRawData()
    {
        return file_get_contents('php://input');
    }
}
?>