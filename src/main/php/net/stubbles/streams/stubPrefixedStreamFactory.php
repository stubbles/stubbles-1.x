<?php
/**
 * Stream factory which prefixes source and target before calling another stream factory.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubPrefixedStreamFactory.php 2324 2009-09-16 11:50:14Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubStreamFactory');
/**
 * Stream factory which prefixes source and target before calling another stream factory.
 *
 * @package     stubbles
 * @subpackage  streams
 */
class stubPrefixedStreamFactory extends stubBaseObject implements stubStreamFactory
{
    /**
     * decorated stream factory
     *
     * @var  stubStreamFactory
     */
    protected $streamFactory;
    /**
     * prefix to add for source and target before calling decorated stream factory
     *
     * @var  string
     */
    protected $prefix;

    /**
     * constructor
     *
     * @param  stubStreamFactory  $streamFactory
     * @param  string             $prefix
     */
    public function __construct(stubStreamFactory $streamFactory, $prefix)
    {
        $this->streamFactory = $streamFactory;
        $this->prefix        = $prefix;
    }

    /**
     * creates an input stream for given source
     *
     * @param   mixed                $source   source to create input stream from
     * @param   array<string,mixed>  $options  list of options for the input stream
     * @return  stubInputStream
     */
    public function createInputStream($source, array $options = array())
    {
        return $this->streamFactory->createInputStream($this->prefix . $source, $options);
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
        return $this->streamFactory->createOutputStream($this->prefix . $target, $options);
    }
}
?>