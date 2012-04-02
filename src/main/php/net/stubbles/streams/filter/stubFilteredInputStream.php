<?php
/**
 * Input stream applying a filter on data read before returning to requestor.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 * @version     $Id: stubFilteredInputStream.php 2295 2009-08-20 21:18:51Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubAbstractDecoratedInputStream',
                      'net::stubbles::streams::filter::stubStreamFilter'
);
/**
 * Input stream applying a filter on data read before returning to requestor.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 */
class stubFilteredInputStream extends stubAbstractDecoratedInputStream
{
    /**
     * stream filter to be applied
     *
     * @var  stubStreamFilter
     */
    protected $streamFilter;

    /**
     * constructor
     *
     * @param  stubInputStream   $inputStream   input stream to filter
     * @param  stubStreamFilter  $streamFilter  stream filter to be applied
     */
    public function __construct(stubInputStream $inputStream, stubStreamFilter $streamFilter)
    {
        parent::__construct($inputStream);
        $this->streamFilter = $streamFilter;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        while ($this->inputStream->eof() === false) {
            $data = $this->inputStream->read($length);
            if ($this->streamFilter->shouldFilter($data) === false) {
                return $data;
            }
        }
        
        return '';
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        while ($this->inputStream->eof() === false) {
            $data = $this->inputStream->readLine($length);
            if ($this->streamFilter->shouldFilter($data) === false) {
                return $data;
            }
        }
        
        return '';
    }
}
?>