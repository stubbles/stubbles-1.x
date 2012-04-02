<?php
/**
 * Class to wrap xincludes transparently as stream wrapper.
 *
 * @package     stubbles
 * @subpackage  xml_xsl
 * @version     $Id: stubXslXIncludeStreamWrapper.php 3237 2011-11-29 15:57:07Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubFileNotFoundException',
                      'net::stubbles::lang::exceptions::stubIOException',
                      'net::stubbles::xml::stubXMLException',
                      'net::stubbles::xml::xsl::stubXSLProcessor'
);
/**
 * Class to wrap xincludes transparently as stream wrapper.
 *
 * @package     stubbles
 * @subpackage  xml_xsl
 */
class stubXslXIncludeStreamWrapper extends stubBaseObject
{
    /**
     * the xsl processor to use for the transformation
     *
     * @var  stubXSLProcessor
     */
    protected static $xslProcessor;
    /**
     * path to cache
     *
     * @var  string
     */
    protected static $cachePath;
    /**
     * mode for cache path if it needs to be created
     *
     * @var  int
     */
    protected static $fileMode;
    /**
     * switch whether caching is enabled or not
     *
     * @var  bool
     */
    protected static $enableCaching    = true;
    /**
     * list of include pathes where files may reside
     *
     * @var  array<string>
     */
    protected static $includePathes    = array();
    /**
     * list of already transformed files
     *
     * @var  array<string>
     */
    protected static $transformedFiles = array();
    /**
     * name of current route
     *
     * @var  string
     */
    protected static $routeName;
    /**
     * current xml file
     *
     * @var  string
     */
    protected $fileName;
    /**
     * file name of the cached file
     *
     * @var  string
     */
    protected $cachedFileName;
    /**
     * project from which the file will be taked
     *
     * @var  string
     */
    protected $project;
    /**
     * the part that will be included
     *
     * @var  string
     */
    protected $part;
    /**
     * current file pointer
     *
     * @var  resource
     */
    protected $fp;

    /**
     * registers the class as stream wrapper for the sml protocol
     *
     * Please note that a cache path is required independent of whether caching
     * is enabled or not. This is due to the fact that the transformed file
     * needs to be saved to disc before its xincludes are resolved because
     * the xincluded files may refer back to the current file with other
     * xincludes.
     *
     * @param   stubXSLProcessor  $xslProcessor
     * @param   string            $defaultIncludePath  default path where to find files to xinclude
     * @param   string            $cachePath           where to cache included files
     * @param   int               $fileMode            mode for cache path if it needs to be created
     * @param   bool              $enableCaching       whether caching is enabled or not
     * @param   string            $routeName           name of route to resolve xincludes for
     * @throws  stubIOException
     */
    public static function register(stubXSLProcessor $xslProcessor, $defaultIncludePath, $cachePath, $fileMode, $enableCaching, $routeName)
    {
        static $registered;
        self::$xslProcessor             = $xslProcessor;
        self::$includePathes['default'] = $defaultIncludePath;
        self::$cachePath                = $cachePath;
        self::$fileMode                 = $fileMode;
        self::$enableCaching            = $enableCaching;
        self::$routeName                = $routeName;
        if (file_exists($cachePath) === false) {
            if (@mkdir($cachePath, self::$fileMode, true) === false) {
                throw new stubIOException('Can not create cache directory ' . $cachePath);
            }
        }
        
        if (true === $registered) {
            return;
        }

        if (stream_wrapper_register('xinc', __CLASS__) === false) {
            throw new stubIOException('A handler has already been registered for the xinc protocol.');
        }

        $registered = true;
    }

    /**
     * checks whether cache is enabled or not
     *
     * @return  bool
     */
    public static function isCacheEnabled()
    {
        return self::$enableCaching;
    }

    /**
     * adds an include path
     *
     * @param  string  $key
     * @param  string  $includePath
     */
    public static function addIncludePath($key, $includePath)
    {
        self::$includePathes[$key] = $includePath;
    }

    /**
     * returns a list of include pathes
     *
     * @return  array<string,string>
     */
    public static function getIncludePathes()
    {
        return self::$includePathes;
    }

    /**
     * open the stream
     *
     * @param   string  $path         the path to open
     * @param   string  $mode         mode for opening
     * @param   string  $options      options for opening
     * @param   string  $opened_path  full path that was actually opened
     * @return  bool
     */
    public function stream_open($path, $mode, $options, $opened_path)
    {
        $this->parsePath($path);
        if (file_exists($this->cachedFileName) === false || $this->needsRefresh() === true) {
            $this->processFile();
        }

        $fp = fopen($this->cachedFileName, 'rb');
        if (false === $fp) {
            return false;
        }

        $this->fp = $fp;
        return true;
    }

    /**
     * check whether the cached file needs to be refreshed
     *
     * @return  bool
     */
    protected function needsRefresh()
    {
        if (in_array($this->cachedFileName, self::$transformedFiles) === true) {
            return false;
        }
        
        if (false === self::$enableCaching) {
            return true;
        }
        
        if (filemtime($this->cachedFileName) > filemtime($this->fileName))  {
            return false;
        }

        return true;
    }

    /**
     * processes the file and creates a cached version of it
     *
     * @throws  stubIOException
     */
    protected function processFile()
    {
        $previousErrorHandling = libxml_use_internal_errors(true);
        $xslProcessor          = clone self::$xslProcessor;
        $xslProcessor->withParameter('', '__file', $this->fileName)
                     ->withParameter('', '__part', $this->part);
        $domDocument           = new DOMDocument();
        if (false === $domDocument->load($this->fileName)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $this->handleErrors($errors, $previousErrorHandling);
        }

        $resultDoc = $xslProcessor->onDocument($domDocument)
                                  ->toDoc();
        // we save first to prevent a infinite loop in case of recursions
        $resultDoc->save($this->cachedFileName);
        $resultDoc->xinclude();
        $errors = libxml_get_errors();
        if (count($errors) > 0) {
            unlink($this->cachedFileName);
            libxml_clear_errors();
            $this->handleErrors($errors, $previousErrorHandling, $domDocument);
        }

        libxml_use_internal_errors($previousErrorHandling);
        $resultDoc->save($this->cachedFileName);
        self::$transformedFiles[] = $this->cachedFileName;
    }

    /**
     * handles libxml errors
     *
     * @param   array             $errors
     * @param   boolean           $previousErrorHandling
     * @param   DOMDocument       $resultDoc
     * @throws  stubXMLException
     */
    protected function handleErrors(array $errors, $previousErrorHandling, DOMDocument $resultDoc = null)
    {
        foreach ($errors as $error) {
            $message = trim($error->message) . (($error->file) ? (' in file ' . $error->file) : ('')) . ' on line ' . $error->line . ' in column ' . $error->column;
            switch ($error->level) {
                case LIBXML_ERR_WARNING:
                    if (null !== $resultDoc) {
                        $this->appendError($resultDoc, 'warning', $message);
                        break;
                    }
                    
                    // break omitted if no result doc given
                
                case LIBXML_ERR_ERROR:
                    if (null !== $resultDoc) {
                        $this->appendError($resultDoc, 'error', $message);
                        break;
                    }
                    
                    // break omitted if no result doc given
                
                case LIBXML_ERR_FATAL:
                    libxml_use_internal_errors($previousErrorHandling);
                    throw new stubXMLException('Fatal error: ' . $message);
                
                default:
                    if (null !== $resultDoc) {
                        $this->appendError($resultDoc, 'warning', $message);
                    }
            }
        }
    }

    /**
     * appends the error message into the result document
     *
     * If a part for the inclusion is known it tries to append the error
     * message into this part, if no part is known the error message is
     * appended directly before the end tag of the root element.
     *
     * @param  DOMDocument  $resultDoc  the document to append the error message into
     * @param  string       $level      level of the error
     * @param  string       $message    the error message
     */
    protected function appendError(DOMDocument $resultDoc, $level, $message)
    {
        $element = $resultDoc->createElement('error', ucfirst($level) . ': ' . $message);
        $element->setAttribute('errorType', $level);
        if (null != $this->part && strlen($this->part) > 0) {
            $xpath = new DOMXPath($resultDoc);
            $entry = $xpath->query("/parts/part[@name='" . $this->part ."']")->item(0);
            if (null !== $entry) {
                $entry->appendChild($element);
            } else {
                $resultDoc->documentElement->appendChild($element);
            }
        } else {
            $resultDoc->documentElement->appendChild($element);
        }
    }

    /**
     * closes the stream
     */
    public function stream_close()
    {
        fclose($this->fp);
    }

    /**
     * read the stream up to $count bytes
     *
     * @param   int     $count  amount of bytes to read
     * @return  string
     */
    public function stream_read($count)
    {
        return fread($this->fp, $count);
    }

    /**
     * checks whether stream is at end of file
     *
     * @return  bool
     */
    public function stream_eof()
    {
        return feof($this->fp);
    }

    /**
     * returns status of stream
     *
     * @return  array
     */
    public function stream_stat()
    {
        return array('size' => filesize($this->cachedFileName));
    }

    /**
     * returns status of url
     *
     * @param   string      $path  path of url to return status for
     * @return  array|bool  false if $path does not exist, else
     */
    public function url_stat($path)
    {
        return array('size' => filesize($this->cachedFileName));
    }

    /**
     * parses the path into class members
     *
     * @param   string  $path
     * @throws  stubFileNotFoundException
     */
    protected function parsePath($path)
    {
        list($project, $fileName, $part) = sscanf($path, 'xinc://%[^/?#]/%[^?]?part=%[^$]');
        if (null !== $fileName) {
            if (isset(self::$includePathes[$project]) === false || file_exists(self::$includePathes[$project] . DIRECTORY_SEPARATOR . $fileName) === false) {
                throw new stubFileNotFoundException(self::$includePathes[$project] . DIRECTORY_SEPARATOR . $fileName);
            }
            
            $this->fileName = self::$includePathes[$project] . DIRECTORY_SEPARATOR . $fileName;
            $cacheKey       = $project . DIRECTORY_SEPARATOR;
        } elseif (file_exists($project) === true) {
            $this->fileName = $project;
            $cacheKey       = '';
        } else {
            throw new stubFileNotFoundException($fileName);
        }

        $this->project = $project;
        $this->part    = $part;
        $locale        = '';
        if (self::$xslProcessor->hasParameter('', 'lang') === true) {
            $locale = self::$xslProcessor->getParameter('', 'lang');
        }
        
        $this->cachedFileName = self::$cachePath . DIRECTORY_SEPARATOR . $cacheKey . $locale  . DIRECTORY_SEPARATOR . self::$routeName  . DIRECTORY_SEPARATOR . $fileName;
        if (file_exists(dirname($this->cachedFileName)) === false) {
            mkdir(dirname($this->cachedFileName), self::$fileMode, true);
        }
    }
}
?>