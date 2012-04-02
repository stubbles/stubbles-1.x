<?php
/**
 * Interface for decorated output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubDecoratedOutputStream.php 2324 2009-09-16 11:50:14Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubOutputStream');
/**
 * Interface for decorated output streams.
 *
 * @package     stubbles
 * @subpackage  streams
 */
interface stubDecoratedOutputStream extends stubOutputStream
{
    /**
     * replace current enclosed output stream
     *
     * @param  stubOutputStream  $outputStream
     */
    public function setEnclosedOutputStream(stubOutputStream $outputStream);

    /**
     * returns enclosed output stream
     *
     * @return  stubOutputStream
     */
    public function getEnclosedOutputStream();
}
?>