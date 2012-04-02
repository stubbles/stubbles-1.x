<?php
/**
 * Bindung module for a default log configuration.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc
 * @version     $Id: stubLogBindingModule.php 2882 2011-01-11 20:54:26Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule');
/**
 * Bindung module for a default log configuration.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc
 */
class stubLogBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * path where logfiles should be stored
     *
     * @var  string
     */
    protected $logPath;
    /**
     * class name of log entry factory class to be bound
     *
     * @var  string
     */
    protected $logEntryFactoryClassName = 'net::stubbles::util::log::entryfactory::stubDefaultLogEntryFactory';
    /**
     * name of class which provides the logger instance
     *
     * @var  string
     * @since  1.3.0
     */
    protected $loggerProviderClassName  = 'net::stubbles::util::log::ioc::stubDefaultLoggerProvider';

    /**
     * constructor
     *
     * Please note that the log path is only optional if it is bound by another
     * module.
     *
     * @param  string  $logPath  optional  path where logfiles should be stored
     */
    public function __construct($logPath = null)
    {
        $this->logPath = $logPath;
    }

    /**
     * static constructor
     *
     * Please note that the log path is only optional if it is bound by another
     * module.
     *
     * @param   string                $logPath  optional
     * @return  stubLogBindingModule
     */
    public static function create($logPath = null)
    {
        return new self($logPath);
    }

    /**
     * sets the class name of log entry factory class to be bound
     *
     * @param   string                $logEntryFactoryClassName
     * @return  stubLogBindingModule
     */
    public function setLogEntryFactoryClassName($logEntryFactoryClassName)
    {
        $this->logEntryFactoryClassName = $logEntryFactoryClassName;
        return $this;
    }

    /**
     * sets name of class which provides the logger instance
     *
     * @param   string                $loggerProviderClassName
     * @return  stubLogBindingModule
     * @since   1.3.0
     */
    public function setLoggerProviderClassName($loggerProviderClassName)
    {
        $this->loggerProviderClassName = $loggerProviderClassName;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        if (null != $this->logPath) {
            $binder->bindConstant()
                   ->named('net.stubbles.log.path')
                   ->to($this->logPath);
        }
        
        $binder->bind('stubLogEntryFactory')
               ->to($this->getLogEntryFactoryClassName())
               ->asSingleton();
        $binder->bind('stubLogger')
               ->named('util.log.baseLogger')
               ->toProviderClass('net::stubbles::util::log::ioc::stubBaseLoggerProvider');
        $binder->bind('stubLogger')
               ->toProviderClass($this->getLoggerProviderClassName());
    }

    /**
     * returns the name of log entry factory class to be used
     *
     * @return  string
     */
    public function getLogEntryFactoryClassName()
    {
        return $this->logEntryFactoryClassName;
    }

    /**
     * returns name of class which provides the logger instance
     *
     * @return  string
     * @since   1.3.0
     */
    public function getLoggerProviderClassName()
    {
        return $this->loggerProviderClassName;
    }
}
?>