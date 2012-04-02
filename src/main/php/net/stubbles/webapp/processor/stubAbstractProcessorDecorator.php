<?php
/**
 * Abstract decorator implementation for processors.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 * @version     $Id: stubAbstractProcessorDecorator.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::processor::stubProcessor');
/**
 * Abstract decorator implementation for processors.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 */
abstract class stubAbstractProcessorDecorator extends stubBaseObject implements stubProcessor
{
    /**
     * decorated processor instance
     *
     * @var  stubProcessor
     */
    protected $processor;

    /**
     * operations to be done before the request is processed
     *
     * @param   stubUriRequest  $uriRequest
     * @return  stubProcessor
     */
    public function startup(stubUriRequest $uriRequest)
    {
        $this->processor->startup($uriRequest);
        return $this;
    }

    /**
     * returns the required role of the user to be able to process the request
     *
     * @param   string  $defaultRole
     * @return  string
     */
    public function getRequiredRole($defaultRole)
    {
        return $this->processor->getRequiredRole($defaultRole);
    }


    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName()
    {
        return $this->processor->getRouteName();
    }

    /**
     * checks whether the current request forces ssl or not
     *
     * @return  bool
     */
    public function forceSsl()
    {
        return $this->processor->forceSsl();
    }

    /**
     * checks whether the request is ssl or not
     *
     * @return  bool
     */
    public function isSsl()
    {
        return $this->processor->isSsl();
    }

    /**
     * checks whether document to generate is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        return $this->processor->isCachable();
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return $this->processor->getCacheVars();
    }

    /**
     * processes the request
     *
     * @return  stubProcessor
     */
    public function process()
    {
        $this->processor->process();
        return $this;
    }

    /**
     * operations to be done after the request was processed
     *
     * @return  stubProcessor
     */
    public function cleanup()
    {
        $this->processor->cleanup();
        return $this;
    }
}
?>