<?php
/**
 * Factory for memory streams.
 *
 * @package     stubbles
 * @subpackage  streams_memory
 * @version     $Id: stubMemoryStreamFactory.php 2324 2009-09-16 11:50:14Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubStreamFactory',
                      'net::stubbles::streams::memory::stubMemoryInputStream',
                      'net::stubbles::streams::memory::stubMemoryOutputStream'
);
/**
 * Factory for memory streams.
 *
 * @package     stubbles
 * @subpackage  streams_memory
 */
class stubMemoryStreamFactory extends stubBaseObject implements stubStreamFactory
{
    /**
     * creates an input stream for given source
     *
     * @param   mixed                $source   source to create input stream from
     * @param   array<string,mixed>  $options  list of options for the input stream
     * @return  stubInputStream
     */
    public function createInputStream($source, array $options = array())
    {
        return new stubMemoryInputStream($source);
    }

    /**
     * creates an output stream for given target
     *
     * @param   mixed                $target   target to create output stream for
     * @param   array<string,mixed>  $options  list of options for the output stream
     * @return  stubOutputStream
     */
    public function createOutputStream($target, array $options = array())
    {
        return new stubMemoryOutputStream();
    }
}
?>