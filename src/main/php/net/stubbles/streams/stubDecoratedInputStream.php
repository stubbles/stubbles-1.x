<?php
/**
 * Interface for decorated input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubDecoratedInputStream.php 2291 2009-08-20 20:14:44Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubInputStream');
/**
 * Interface for decorated input streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
interface stubDecoratedInputStream extends stubInputStream
{
    /**
     * replace current enclosed input stream
     *
     * @param  stubInputStream  $inputStream
     */
    public function setEnclosedInputStream(stubInputStream $inputStream);

    /**
     * returns enclosed input stream
     *
     * @return  stubInputStream
     */
    public function getEnclosedInputStream();
}
?>