<?php
/**
 * Extended Reflection class for packages.
 *
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionPackage.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubBaseReflectionClass',
                      'net::stubbles::reflection::stubReflectionClass'
);
/**
 * Extended Reflection class for packages.
 *
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionPackage
{
    /**
     * the package separator character
     */
    const SEPARATOR = '::';
    /**
     * name of the reflected package
     *
     * @var  string
     */
    protected $packageName;

    /**
     * constructor
     *
     * @param  string  $packageName  name of package to reflect
     */
    public function __construct($packageName)
    {
        $this->packageName = $packageName;
    }

    /**
     * checks whether a value is equal to the package
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->packageName == $this->packageName);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionPackage['[name-of-reflected-package]']  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionPackage[mypackage] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionPackage[' . $this->packageName . "] {\n}\n";
    }

    /**
     * returns the full qualified class name of the reflected package
     *
     * @return  string
     */
    public function getName()
    {
        return $this->packageName;
    }

    /**
     * checks whether the package has a class with the given name
     *
     * @param   string  $nqClassName  non qualified name of class to check
     * @return  bool
     */
    public function hasClass($nqClassName)
    {
        $classNames = stubClassLoader::getClassNames($this->packageName, false);
        return in_array($this->packageName . self::SEPARATOR . $nqClassName, $classNames);
    }

    /**
     * returns the specified class
     *
     * @param   string               $nqClassName  non qualified name of class to return
     * @return  stubReflectionClass
     * @throws  stubClassNotFoundException
     */
    public function getClass($nqClassName)
    {
        $stubRefClass = new stubReflectionClass($this->packageName . self::SEPARATOR . $nqClassName);
        return $stubRefClass;
    }

    /**
     * returns a list of all class names within the package
     *
     * @param   bool           $recursive  optional  true if classes of subpackages should be included
     * @return  array<string>
     */
    public function getClassNames($recursive = false)
    {
        return stubClassLoader::getClassNames($this->packageName, $recursive);
    }

    /**
     * returns a list of all classes within the package
     *
     * @param   bool                        $recursive  optional  true if classes of subpackages should be included
     * @return  array<stubReflectionClass>
     */
    public function getClasses($recursive = false)
    {
        $classes = array();
        $classNames = stubClassLoader::getClassNames($this->packageName, $recursive);
        foreach ($classNames as $fqClassName) {
            $classes[] = new stubReflectionClass($fqClassName);
        }

        return $classes;
    }

    /**
     * check whether the package contains a subpackage with the given name
     *
     * @param   string  $name  name of subpackage
     * @return  bool
     */
    public function hasPackage($name)
    {
        return in_array($name, $this->getPackageNames(true));
    }

    /**
     * returns the subpackage
     *
     * @param   string                 $name  name of subpackage (without name of current package)
     * @return  stubReflectionPackage
     */
    public function getPackage($name)
    {
        return new self($this->packageName . self::SEPARATOR . $name);
    }

    /**
     * returns a list of all subpackage names
     *
     * @param   bool           $recursive  optional  true if subpackages of subpackages should be included
     * @return  array<string>
     */
    public function getPackageNames($recursive = false)
    {
        $packages   = array();
        $classNames = stubClassLoader::getClassNames($this->packageName, true);
        foreach ($classNames as $fqClassName) {
            $packageName = stubClassLoader::getPackageName($fqClassName);
            if ($packageName == $this->packageName) {
                continue;
            }

            $shortName = str_replace($this->packageName . self::SEPARATOR, '', $packageName);
            if (strstr($shortName, self::SEPARATOR) !== false && false === $recursive) {
                continue;
            }

            $packages[$shortName] = $shortName;
        }

        $return = array_keys($packages);
        sort($return);
        return $return;
    }

    /**
     * returns a list of all subpackages
     *
     * @param   bool                          $recursive  optional  true if subpackages of subpackages should be included
     * @return  array<stubReflectionPackage>
     */
    public function getPackages($recursive = false)
    {
        $packages    = $this->getPackageNames($recursive);
        $refPackages = array();
        foreach ($packages as $package) {
            $refPackages[] = new self($this->packageName . self::SEPARATOR . $package);
        }

        return $refPackages;
    }
}
?>