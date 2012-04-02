<?php
/**
 * Class for starting the application by configuring the IoC container.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubApp.php 3220 2011-11-14 15:33:46Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubArgumentsBindingModule',
                      'net::stubbles::ioc::module::stubBindingModule',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException'
);
/**
 * Class for starting the application by configuring the IoC container.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubApp extends stubBaseObject
{
    /**
     * configures the application using the given binding modules and returns
     * injector so that the bootstrap file can request an instance of the entry
     * class
     *
     * @return  stubInjector
     */
    public static function createInjector()
    {
        return self::createInjectorWithBindings(self::extractArgs(func_get_args()));
    }

    /**
     * configures the application using the given binding modules and returns
     * a front controller instance
     *
     * @return  stubFrontController
     * @deprecated  use createWebApp() instead, will be removed with 1.8.0 or 2.0.0
     */
    public static function createFrontController()
    {
        return self::createInjectorWithBindings(self::extractArgs(func_get_args()))
                   ->getInstance('net::stubbles::websites::stubFrontController');
    }

    /**
     * extracts arguments
     *
     * If arguments has only one value and this is an array this will be returned,
     * else all arguments will be returned.
     *
     * @param   array                            $args
     * @return  array<string|stubBindingModule>
     */
    protected static function extractArgs(array $args)
    {
        if (count($args) === 1 && is_array($args[0]) === true) {
            return $args[0];
        }

        return $args;
    }

    /**
     * creates an object via injection
     *
     * If the class to create an instance of contains a static __bindings() method
     * this method will be used to configure the ioc bindings before using the ioc
     * container to create the instance.
     *
     * @param   string         $fqClassName  full qualified class name of class to create an instance of
     * @param   string         $projectPath  path to project
     * @param   array<string>  $argv         optional  list of arguments
     * @return  object
     */
    public static function createInstance($fqClassName, $projectPath, array $argv = null)
    {
        return self::createInjectorWithBindings(self::getBindingsForClass($fqClassName, $projectPath, $argv))
                   ->getInstance($fqClassName);
    }

    /**
     * creates list of bindings from given class
     *
     * @param   string                           $fqClassName  full qualified class name of class to create an instance of
     * @param   string                           $projectPath  path to project
     * @param   array<string>                    $argv         optional  list of arguments
     * @return  array<string|stubBindingModule>
     * @since   1.3.0
     */
    public static function getBindingsForClass($fqClassName, $projectPath, array $argv = null)
    {
        $nqClassName = stubClassLoader::getNonQualifiedClassName($fqClassName);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($fqClassName);
        }

        $bindings = array();
        if (method_exists($nqClassName, '__bindings') === true) {
            $bindings = call_user_func_array(array($nqClassName, '__bindings'), array($projectPath));
        }

        if (null !== $argv) {
            $bindings[] = new stubArgumentsBindingModule($argv);
        }

        return $bindings;
    }

    /**
     * configures the application using the given binding modules and returns
     * injector so that the bootstrap file can request an instance of the entry
     * class
     *
     * @param   array<string|stubBindingModule>  $bindingModules
     * @return  stubInjector
     */
    public static function createInjectorWithBindings(array $bindingModules)
    {
        return self::createBinderWithBindings($bindingModules)->getInjector();
    }

    /**
     * configures the application using the given binding modules and returns
     * binder so that the bootstrap file can request an instance of the entry
     * class
     *
     * @param   array<string|stubBindingModule>  $bindingModules
     * @return  stubBinder
     * @throws  stubIllegalArgumentException
     * @since   1.3.0
     */
    public static function createBinderWithBindings(array $bindingModules)
    {
        $binder = new stubBinder();
        foreach ($bindingModules as $bindingModule) {
            if (is_string($bindingModule) === true) {
                $nqClassName = stubClassLoader::getNonQualifiedClassName($bindingModule);
                if (class_exists($nqClassName, false) === false) {
                    stubClassLoader::load($bindingModule);
                }

                $bindingModule = new $nqClassName();
            }

            if (($bindingModule instanceof stubBindingModule) === false) {
                throw new stubIllegalArgumentException('Given module class ' . get_class($bindingModule) . ' is not an instance of net::stubbles::ioc::module::stubBindingModule');
            }

            /* @var  $bindingModule  stubBindingModule */
            $bindingModule->configure($binder);
        }

        // make injector itself available for injection
        $binder->bind('stubInjector')
               ->toInstance($binder->getInjector());
        return $binder;
    }
}
?>