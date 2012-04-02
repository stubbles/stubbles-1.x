<?php
/**
 * Class loader for all stubbles classes.
 *
 * @package  stubbles
 * @version  $Id: stubClassLoader.php 3174 2011-08-26 12:21:04Z mikey $
 */
/**
 * Interface for class loaders that load classes from foreign namespaces.
 *
 * This interface must reside here because the stubClassLoader uses it as type hint.
 *
 * @package  stubbles
 */
interface stubForeignClassLoader
{
    /**
     * sets the namespace where this classloader is responsible for
     *
     * @param  string  $namespace
     */
    public function setNamespace($namespace);

    /**
     * returns the namespace where this classloader is responsible for
     *
     * @return  string
     */
    public function getNamespace();

    /**
     * loads the given class
     *
     * @param   string  $fqClassName  the full qualified class name of the class to load
     * @throws  stubClassNotFoundException
     */
    public function load($fqClassName);
}
/**
 * Exception thrown if the class loader can not find the desired class.
 *
 * This exception must reside here because the stubClassLoader uses it when a class can not be loaded.
 *
 * @package  stubbles
 */
class stubClassNotFoundException extends Exception
{
    /**
     * full qualified class name of the class that was not found
     *
     * @var  string
     */
    protected $fqClassName;

    /**
     * constructor
     *
     * @param  string  $fqClassName         full qualified class name of the class that was not found
     * @param  bool    $foreignClassLoader  optional  true if thrown in stubForeignClassLoader instance
     */
    public function __construct($fqClassName, $foreignClassLoader = false)
    {
        $this->fqClassName = $fqClassName;
        $caller  = debug_backtrace();
        $file   = ((false == $foreignClassLoader) ? ($caller[1]['file']) : ($caller[2]['file']));
        $line   = ((false == $foreignClassLoader) ? ($caller[1]['line']) : ($caller[2]['line']));
        $message = 'The class ' . $this->fqClassName . ' loaded in ' . $file . ' on line ' . $line . ' was not found.';
        parent::__construct($message);
    }

    /**
     * returns the full qualified class name of the class that was not found
     *
     * @return  string
     */
    public function getNotFoundClassName()
    {
        return $this->fqClassName;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * <code>
     * net::stubbles::stubClassNotFoundException {
     *     message(string): The class example::Foo loaded in bar.php on line 6 was not found.
     *     classname(string): example::Foo
     *     file(string): stubClassLoader.php
     *     line(integer): 179
     *     code(integer): 0
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        $string  = "net::stubbles::stubClassNotFoundException {\n";
        $string .= '    message(string): ' . $this->getMessage() . "\n";
        $string .= '    classname(string): ' . $this->fqClassName . "\n";
        $string .= '    file(string): ' . $this->getFile() . "\n";
        $string .= '    line(integer): ' . $this->getLine() . "\n";
        $string .= '    code(integer): ' . $this->getCode() . "\n";
        $string .= "}\n";
        return $string;
    }
}
/**
 * Class loader for all stubbles classes.
 *
 * The class loader takes care that all class files are only loaded once. It
 * allows all classes to include the required files without knowing where they
 * reside or if they have been loaded before.
 *
 * @static
 * @package  stubbles
 */
class stubClassLoader
{
    /**
     * class mapping for moved classes
     *
     * @var  array<string,string>
     */
    private static $classMapping        = array('net::stubbles::websites::cache::stubAbstractWebsiteCache'                   => 'net::stubbles::webapp::cache::stubAbstractWebsiteCache',
                                                'net::stubbles::websites::cache::stubCachingProcessor'                       => 'net::stubbles::webapp::cache::stubCachingProcessor',
                                                'net::stubbles::websites::cache::stubCompositeWebsiteCache'                  => 'net::stubbles::webapp::cache::stubCompositeWebsiteCache',
                                                'net::stubbles::websites::cache::stubDefaultWebsiteCache'                    => 'net::stubbles::webapp::cache::stubDefaultWebsiteCache',
                                                'net::stubbles::websites::cache::stubDummyWebsiteCache'                      => 'net::stubbles::webapp::cache::stubDummyWebsiteCache',
                                                'net::stubbles::websites::cache::stubGzipWebsiteCache'                       => 'net::stubbles::webapp::cache::stubGzipWebsiteCache',
                                                'net::stubbles::websites::cache::stubLoggingWebsiteCache'                    => 'net::stubbles::webapp::cache::stubLoggingWebsiteCache',
                                                'net::stubbles::websites::cache::stubWebsiteCache'                           => 'net::stubbles::webapp::cache::stubWebsiteCache',
                                                'net::stubbles::websites::cache::stubWebsiteCacheProvider'                   => 'net::stubbles::webapp::cache::stubWebsiteCacheProvider',
                                                'net::stubbles::websites::processors::auth::stubAuthHandler'                 => 'net::stubbles::webapp::auth::stubAuthHandler',
                                                'net::stubbles::websites::processors::auth::stubAuthProcessor'               => 'net::stubbles::webapp::auth::stubAuthProcessor',
                                                'net::stubbles::websites::processors::routing::stubAbstractProcessable'      => 'net::stubbles::webapp::xml::route::stubAbstractProcessable',
                                                'net::stubbles::websites::processors::routing::stubProcessable'              => 'net::stubbles::webapp::xml::route::stubProcessable',
                                                'net::stubbles::websites::processors::routing::stubRoute'                    => 'net::stubbles::webapp::xml::route::stubRoute',
                                                'net::stubbles::websites::processors::stubAbstractProcessor'                 => 'net::stubbles::webapp::processor::stubAbstractProcessor',
                                                'net::stubbles::websites::processors::stubAbstractProcessorDecorator'        => 'net::stubbles::webapp::processor::stubAbstractProcessorDecorator',
                                                'net::stubbles::websites::processors::stubProcessor'                         => 'net::stubbles::webapp::processor::stubProcessor',
                                                'net::stubbles::websites::processors::stubProcessorException'                => 'net::stubbles::webapp::processor::stubProcessorException',
                                                'net::stubbles::websites::xml::generator::stubModeXMLGenerator'              => 'net::stubbles::webapp::xml::generator::stubModeXmlGenerator',
                                                'net::stubbles::websites::xml::generator::stubRequestXMLGenerator'           => 'net::stubbles::webapp::xml::generator::stubRequestXmlGenerator',
                                                'net::stubbles::websites::xml::generator::stubRouteXMLGenerator'             => 'net::stubbles::webapp::xml::generator::stubRouteXmlGenerator',
                                                'net::stubbles::websites::xml::generator::stubSessionXMLGenerator'           => 'net::stubbles::webapp::xml::generator::stubSessionXmlGenerator',
                                                'net::stubbles::websites::xml::generator::stubVariantListGenerator'          => 'net::stubbles::webapp::xml::generator::stubVariantListGenerator',
                                                'net::stubbles::websites::xml::generator::stubXMLGenerator'                  => 'net::stubbles::webapp::xml::generator::stubXmlGenerator',
                                                'net::stubbles::websites::xml::generator::stubXMLGeneratorFacade'            => 'net::stubbles::webapp::xml::generator::stubXmlGeneratorFacade',
                                                'net::stubbles::websites::xml::routing::stubAbstractXmlFormProcessable'      => 'net::stubbles::webapp::xml::route::stubAbstractXmlFormProcessable',
                                                'net::stubbles::websites::xml::routing::stubXmlFormProcessable'              => 'net::stubbles::webapp::xml::route::stubXmlFormProcessable',
                                                'net::stubbles::websites::xml::skin::stubCachingSkinGenerator'               => 'net::stubbles::webapp::xml::skin::stubCachingSkinGenerator',
                                                'net::stubbles::websites::xml::skin::stubDefaultSkinGenerator'               => 'net::stubbles::webapp::xml::skin::stubDefaultSkinGenerator',
                                                'net::stubbles::websites::xml::skin::stubSkinGenerator'                      => 'net::stubbles::webapp::xml::skin::stubSkinGenerator',
                                                'net::stubbles::websites::xml::skin::stubSkinGeneratorProvider'              => 'net::stubbles::webapp::xml::skin::stubSkinGeneratorProvider',
                                                'net::stubbles::websites::xml::stubShowLastXMLInterceptor'                   => 'net::stubbles::webapp::xml::stubShowLastXmlInterceptor',
                                                'net::stubbles::websites::xml::stubXMLProcessor'                             => 'net::stubbles::webapp::xml::stubXmlProcessor',
                                                'net::stubbles::websites::xml::stubXmlProcessorTramsformer'                  => 'net::stubbles::webapp::xml::stubXmlProcessorTramsformer',
                                                'net::stubbles::websites::variantmanager::types::stubAbstractVariant'        => 'net::stubbles::webapp::variantmanager::types::stubAbstractVariant',
                                                'net::stubbles::websites::variantmanager::types::stubConfigurableVariant'    => 'net::stubbles::webapp::variantmanager::types::stubConfigurableVariant',
                                                'net::stubbles::websites::variantmanager::types::stubDummyVariant'           => 'net::stubbles::webapp::variantmanager::types::stubDummyVariant',
                                                'net::stubbles::websites::variantmanager::types::stubLeadVariant'            => 'net::stubbles::webapp::variantmanager::types::stubLeadVariant',
                                                'net::stubbles::websites::variantmanager::types::stubRandomVariant'          => 'net::stubbles::webapp::variantmanager::types::stubRandomVariant',
                                                'net::stubbles::websites::variantmanager::types::stubRequestParamVariant'    => 'net::stubbles::webapp::variantmanager::types::stubRequestParamVariant',
                                                'net::stubbles::websites::variantmanager::types::stubRootVariant'            => 'net::stubbles::webapp::variantmanager::types::stubRootVariant',
                                                'net::stubbles::websites::variantmanager::types::stubVariant'                => 'net::stubbles::webapp::variantmanager::types::stubVariant',
                                                'net::stubbles::websites::variantmanager::stubAbstractVariantFactory'        => 'net::stubbles::webapp::variantmanager::stubAbstractVariantFactory',
                                                'net::stubbles::websites::variantmanager::stubVariantConfigurationException' => 'net::stubbles::webapp::variantmanager::stubVariantConfigurationException',
                                                'net::stubbles::websites::variantmanager::stubVariantFactory'                => 'net::stubbles::webapp::variantmanager::stubVariantFactory',
                                                'net::stubbles::websites::variantmanager::stubVariantSettingPreInterceptor'  => 'net::stubbles::webapp::variantmanager::stubVariantSettingPreInterceptor',
                                                'net::stubbles::websites::variantmanager::stubVariantSwitchPreInterceptor'   => 'net::stubbles::webapp::variantmanager::stubVariantSwitchPreInterceptor',
                                                'net::stubbles::websites::variantmanager::stubVariantCookieCreator'          => 'net::stubbles::webapp::variantmanager::stubVariantCookieCreator',
                                                'net::stubbles::websites::variantmanager::stubVariantsMap'                   => 'net::stubbles::webapp::variantmanager::stubVariantsMap',
                                                'net::stubbles::websites::variantmanager::stubVariantsPreInterceptor'        => 'net::stubbles::webapp::variantmanager::stubVariantsPreInterceptor',
                                                'net::stubbles::websites::variantmanager::stubXmlVariantFactory'             => 'net::stubbles::webapp::variantmanager::stubXmlVariantFactory'
                                          );
    /**
     * contains a mapping of non qualified class names to their full qualified names
     *
     * @var  array<string,string>
     */
    private static $classNames          = array('net::stubbles::stubClassLoader'            => 'stubClassLoader',
                                                'net::stubbles::stubClassNotFoundException' => 'stubClassNotFoundException',
                                                'net::stubbles::stubForeignClassLoader'     => 'stubForeignClassLoader'
                                          );
    /**
     * list of foreign class loaders
     *
     * @var  array<stubForeignClassLoader>
     */
    private static $foreignClassLoaders = array();
    /**
     * switch whether to use star files or not
     *
     * @var  bool
     */
    private static $useStar             = null;
    /**
     * path to source files
     *
     * @var  string
     */
    private static $sourcePath          = null;

    /**
     * does some initializing
     */
    private static function init()
    {
        self::$useStar    = class_exists('StarClassRegistry', false);
        self::$sourcePath = stubBootstrap::getSourcePath() . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR;
    }

    /**
     * does a class name mapping for bc
     *
     * @param   string  $fqClassName
     * @return  string
     */
    private static function mapClassName($fqClassName)
    {
        if (isset(self::$classMapping[$fqClassName]) === true) {
            return self::$classMapping[$fqClassName];
        }

        return $fqClassName;
    }

    /**
     * method to load files from source path
     *
     * Usage: stubblesClassLoader::load('path::to::Classfile');
     * or load more than one at once:
     * stubblesClassLoader::load('path::to::first::Class',
     *                           'path::to::second.Class'
     * );
     * You may name as many files as you like, there is no restriction
     * on the number of arguments.
     *
     * @throws  stubClassNotFoundException
     */
    public static function load()
    {
        $classNames = func_get_args();
        if (count($classNames) === 0) {
            // its ok to call this without any arguments, this won't cause any harm
            return;
        }

        if (null === self::$useStar) {
            self::init();
        }

        foreach ($classNames as $fqClassName) {
            $fqClassName = self::mapClassName($fqClassName);
            $nqClassName = self::getNonQualifiedClassName($fqClassName);
            if (isset(self::$classNames[$nqClassName]) === true) {
                continue;
            }

            self::$classNames[$nqClassName] = $fqClassName;
            $foreignNamespace = self::getForeignNamespace($fqClassName);
            if (null !== $foreignNamespace) {
                self::$foreignClassLoaders[$foreignNamespace][0]->load($fqClassName);
                return;
            }

            $uri = null;
            if (true === self::$useStar) {
                $uri = StarClassRegistry::getUriForClass($fqClassName);
            }
            if (null === $uri) {
                $uri = self::$sourcePath .  str_replace('::', DIRECTORY_SEPARATOR, $fqClassName) . '.php';
            }

            if ((include_once $uri) === false) {
                throw new stubClassNotFoundException($fqClassName);
            }

            if (method_exists($nqClassName, '__static') === true) {
                call_user_func(array($nqClassName, '__static'));
            }
        }
    }

    /**
     * returns a list of all available classnames within a package
     *
     * @param   string         $packageName  name of the package to retrieve class names for
     * @param   bool           $recursive    optional  true if classes of subpackages should be included
     * @return  array<string>
     */
    public static function getClassNames($packageName, $recursive = false)
    {
        if (null == self::$useStar) {
            self::init();
        }

        $dirName = self::$sourcePath . str_replace('::', DIRECTORY_SEPARATOR, $packageName);
        if (file_exists($dirName) == false) {
            return array();
        }

        if (false === $recursive) {
            $dirIt = new DirectoryIterator($dirName);
        } else {
            $dirIt = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirName));
        }

        $classes = array();
        foreach ($dirIt as $file) {
            if ($file->isDir() == true || substr($file->getFilename(), -4) != '.php') {
                continue;
            }

            $classes[] = str_replace(DIRECTORY_SEPARATOR, '::', str_replace('.php', '', str_replace(self::$sourcePath, '', $file->getPathname())));
        }

        if (true === self::$useStar) {
            $starClasses = StarClassRegistry::getClasses();
            foreach ($starClasses as $fqClassName) {
                if (substr($fqClassName, 0, strlen($packageName)) !== $packageName) {
                    continue;
                }

                $nqClassName = self::getNonQualifiedClassName($fqClassName);
                if (str_replace($nqClassName, '', $fqClassName) != $packageName && false === $recursive) {
                    continue;
                }

                $classes[] = $fqClassName;
            }
        }

        sort($classes);
        return $classes;
    }

    /**
     * returns the non qualified class name from a full qualified class name
     *
     * @param   string  $fqClassName
     * @return  string
     */
    public static function getNonQualifiedClassName($fqClassName)
    {
        $classNameParts = explode('::', $fqClassName);
        return $classNameParts[count($classNameParts) - 1];
    }

    /**
     * returns the full qualified class name of a non qualified class name
     *
     * @param   string  $nqClassName
     * @return  string
     */
    public static function getFullQualifiedClassName($nqClassName)
    {
        if (isset(self::$classNames[$nqClassName]) == true) {
            return self::$classNames[$nqClassName];
        }

        return null;
    }

    /**
     * returns the package name of a class
     *
     * @param   string  $fqClassName
     * @return  string
     */
    public static function getPackageName($fqClassName)
    {
        $classNameParts = explode('::', $fqClassName);
        unset($classNameParts[count($classNameParts) - 1]);
        return join('::', $classNameParts);
    }

    /**
     * registers a foreign class loader
     *
     * @param  stubForeignClassLoader  $foreignClassLoader
     */
    public static function registerForeignClassLoader(stubForeignClassLoader $foreignClassLoader)
    {
        self::$foreignClassLoaders[$foreignClassLoader->getNamespace()] = array($foreignClassLoader, strlen($foreignClassLoader->getNamespace()));
    }

    /**
     * checks whether a class resides in a foreign namespace
     *
     * @param   string  $fqClassName  the class to check if it is in a foreign namespace
     * @return  string  the foreign namespace, null if class is not in any one
     */
    private static function getForeignNamespace($fqClassName)
    {
        foreach (self::$foreignClassLoaders as $foreignNamespace => $foreignClassLoader) {
            if (strncmp($fqClassName, $foreignNamespace, $foreignClassLoader[1]) === 0) {
                return $foreignNamespace;
            }
        }

        return null;
    }
}
/**
 * set internal, input and output encoding
 */
iconv_set_encoding('internal_encoding', 'UTF-8');
if (($ctype = getenv('LC_CTYPE')) || ($ctype = setlocale(LC_CTYPE, 0))) {
    sscanf($ctype, '%[^.].%s', $language, $charset);
    if (is_numeric($charset) === true) {
        $charset = 'CP' . $charset;
    } elseif (null == $charset) {
        $charset = 'iso-8859-1';
    }
    
    iconv_set_encoding('output_encoding', $charset);
    iconv_set_encoding('input_encoding', $charset);
}
/**
 * make the stubObject class available so there is no need to include it in
 * every other class that should extend it
 */
stubClassLoader::load('net::stubbles::lang::stubObject',
                      'net::stubbles::lang::stubBaseObject',
                      'net::stubbles::lang::exceptions::stubThrowable',
                      'net::stubbles::lang::exceptions::stubException',
                      'net::stubbles::lang::exceptions::stubRuntimeException',
                      'net::stubbles::lang::serialize::stubSerializable',
                      'net::stubbles::lang::serialize::stubSerializableObject',
                      'net::stubbles::lang::serialize::stubSerializedObject'
);
?>