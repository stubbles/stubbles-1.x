<?php
/**
 * Injection provider for logger instances.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc
 * @version     $Id: stubBaseLoggerProvider.php 2060 2009-01-26 12:57:25Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::util::log::stubLogEntryFactory',
                      'net::stubbles::util::log::stubLogger'
);
/**
 * Injection provider for logger instances.
 *
 * Please note that this provider requires injections as well and thus should
 * only be added to the binding via the addProviderClass() method.
 *
 * Additionally note that this provider only creates logger instances without
 * any log appenders.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc
 */
class stubBaseLoggerProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * factory to be used to create log entry containers
     *
     * @var  stubLogEntryFactory
     */
    protected $logEntryFactory;

    /**
     * constructor
     *
     * @param  stubLogEntryFactory  $logEntryFactory  factory to be used to create log entry containers
     * @Inject
     */
    public function __construct(stubLogEntryFactory $logEntryFactory)
    {
        $this->logEntryFactory = $logEntryFactory;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return new stubLogger($this->logEntryFactory);
    }
}
?>