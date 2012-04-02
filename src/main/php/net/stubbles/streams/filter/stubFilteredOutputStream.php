<?php
/**
 * Output stream applying a filter on data to write.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 * @version     $Id: stubFilteredOutputStream.php 2324 2009-09-16 11:50:14Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubAbstractDecoratedOutputStream',
                      'net::stubbles::streams::filter::stubStreamFilter'
);
/**
 * Output stream applying a filter on data to write.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 */
class stubFilteredOutputStream extends stubAbstractDecoratedOutputStream
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
     * @param  stubOutputStream  $outputStream  stream to apply filter onto
     * @param  stubStreamFilter  $streamFilter  stream filter to be applied
     */
    public function __construct(stubOutputStream $outputStream, stubStreamFilter $streamFilter)
    {
        parent::__construct($outputStream);
        $this->streamFilter = $streamFilter;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        if ($this->streamFilter->shouldFilter($bytes) === false) {
            return $this->outputStream->write($bytes);
        }
        
        return 0;
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        if ($this->streamFilter->shouldFilter($bytes) === false) {
            return $this->outputStream->writeLine($bytes);
        }
        
        return 0;
    }
}
?>