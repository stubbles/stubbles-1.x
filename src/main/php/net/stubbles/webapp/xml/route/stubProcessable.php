<?php
/**
 * Interface for a processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id: stubProcessable.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::ipo::response::stubResponse'
);
/**
 * Interface for a processable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 */
interface stubProcessable extends stubObject
{
    /**
     * sets the context
     *
     * @param   array<string,mixed>  $context
     * @return  stubProcessable
     */
    public function setContext(array $context);

    /**
     * operations to be done before processing is done
     *
     * The startup() method is called after construction, any setter with
     * an @Inject annotation and setContext(), but before any other method and
     * can be used to set up the internal state of the processable properly.
     */
    public function startup();

    /**
     * checks whether the processable is available or not
     *
     * @return  bool
     */
    public function isAvailable();

    /**
     * checks whether processable is cachable or not
     *
     * @return  bool
     */
    public function isCachable();

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars();

    /**
     * processes the processable
     *
     * @return  mixed
     */
    public function process();

    /**
     * operations to be done after processing is done
     *
     * The cleanup() method is called in every case, even if the process()
     * method was not called because the route result was retrieved from cache or
     * if the process() method throwed a stubProcessorException.
     */
    public function cleanup();
}
?>