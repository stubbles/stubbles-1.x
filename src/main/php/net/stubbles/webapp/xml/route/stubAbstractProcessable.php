<?php
/**
 * Abstract base implementation of a processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id: stubAbstractProcessable.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestPrefixDecorator',
                      'net::stubbles::webapp::xml::route::stubProcessable'
);
/**
 * Abstract base implementation of a processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 */
abstract class stubAbstractProcessable extends stubBaseObject implements stubProcessable
{
    /**
     * request instance
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * context with additional information
     *
     * @var  array<string,mixed>
     */
    protected $context = array();

    /**
     * sets the context
     *
     * @param   array<string,mixed>  $context
     * @return  stubProcessable
     */
    public function setContext(array $context)
    {
        $this->context = $context;
        if ($this->request instanceof stubRequest && isset($context['prefix']) === true) {
            $this->request = new stubRequestPrefixDecorator($this->request, $context['prefix']);
        }
        
        return $this;
    }

    /**
     * operations to be done before processing is done
     */
    public function startup()
    {
        // nothing to do
    }

    /**
     * checks whether the processable is available or not
     *
     * @return  bool
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * operations to be done after processing is done
     */
    public function cleanup()
    {
        // nothing to do
    }
}
?>