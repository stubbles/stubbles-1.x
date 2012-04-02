<?php
/**
 * Interface for stream factories.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubStreamFactory.php 2324 2009-09-16 11:50:14Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubInputStream',
                      'net::stubbles::streams::stubOutputStream'
);
/**
 * Interface for stream factories.
 *
 * @package     stubbles
 * @subpackage  streams
 */
interface stubStreamFactory extends stubObject
{
    /**
     * creates an input stream for given source
     *
     * @param   mixed                $source   source to create input stream from
     * @param   array<string,mixed>  $options  list of options for the input stream
     * @return  stubInputStream
     */
    public function createInputStream($source, array $options = array());

    /**
     * creates an output stream for given target
     *
     * @param   mixed                $target   target to create output stream for
     * @param   array<string,mixed>  $options  list of options for the output stream
     * @return  stubOutputStream
     */
    public function createOutputStream($target, array $options = array());
}
?>