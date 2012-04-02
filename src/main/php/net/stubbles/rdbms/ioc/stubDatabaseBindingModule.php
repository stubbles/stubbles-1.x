<?php
/**
 * Binding module for the database package.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc
 * @version     $Id: stubDatabaseBindingModule.php 2512 2010-03-03 12:57:14Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule');
/**
 * Binding module for the database package.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc
 */
class stubDatabaseBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * whether to allow fallback to default connection if named connection does not exist
     *
     * @var  bool
     */
    protected $fallback;
    /**
     * descriptor to be used for initializer
     *
     * @var  string
     */
    protected $descriptor;
    /**
     * name of database initializer class to bind
     *
     * @var  string
     */
    protected $databaseInitializerClassName        = 'net::stubbles::rdbms::stubPropertyBasedDatabaseInitializer';
    /**
     * name of database connection provider class to bind
     *
     * @var  string
     */
    protected $databaseConnectionProviderClassName = 'net::stubbles::rdbms::ioc::stubDatabaseConnectionProvider';

    /**
     * constructor
     *
     * @param  bool    $fallback    optional  whether to allow fallback to default connection if named connection does not exist
     * @param  string  $descriptor  optional  descriptor for database initializer
     */
    public function __construct($fallback = true, $descriptor = null)
    {
        $this->fallback   = $fallback;
        $this->descriptor = $descriptor;
    }

    /**
     * static constructor
     *
     * @param   bool                       $fallback    optional  whether to allow fallback to default connection if named connection does not exist
     * @param   string                     $descriptor  optional  descriptor for database initializer
     * @return  stubDatabaseBindingModule
     * @since   1.2.0
     */
    public static function create($fallback = true, $descriptor = null)
    {
        return new self($fallback, $descriptor);
    }

    /**
     * sets name of database initializer class to bind
     *
     * @param   string                     $databaseInitializerClassName
     * @return  stubDatabaseBindingModule
     * @since   1.2.0
     */
    public function setDatabaseInitializerClassName($databaseInitializerClassName)
    {
        $this->databaseInitializerClassName = $databaseInitializerClassName;
        return $this;
    }

    /**
     * sets name of database connection provider class to bind
     *
     * @param   string                     $databaseConnectionProviderClassName
     * @return  stubDatabaseBindingModule
     * @since   1.2.0
     */
    public function setDatabaseConnectionProviderClassName($databaseConnectionProviderClassName)
    {
        $this->databaseConnectionProviderClassName = $databaseConnectionProviderClassName;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bind('stubDatabaseInitializer')
               ->to($this->databaseInitializerClassName);
        $binder->bind('stubDatabaseConnection')
               ->toProviderClass($this->databaseConnectionProviderClassName);
        $binder->bindConstant()
               ->named('net.stubbles.rdbms.fallback')
               ->to($this->fallback);
        if (null !== $this->descriptor) {
            $binder->bindConstant()
                   ->named('net.stubbles.rdbms.descriptor')
                   ->to($this->descriptor);
        }
    }
}
?>