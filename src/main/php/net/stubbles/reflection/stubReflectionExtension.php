<?php
/**
 * Extended Reflection class for extensions.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionExtension.php 2989 2011-02-11 18:35:45Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::stubReflectionFunction'
);
/**
 * Extended Reflection class for extensions.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionExtension extends ReflectionExtension
{
    /**
     * name of reflected extension
     *
     * @var  string
     */
    protected $extensionName;
    
    /**
     * constructor
     *
     * @param  string  $extensionName  name of extension to reflect
     */
    public function __construct($extensionName)
    {
        parent::__construct($extensionName);
        $this->extensionName = $extensionName;
    }
    
    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->extensionName == $this->extensionName);
        }
        
        return false;
    }
    
    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionExtension['[name-of-reflected-extension]']  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionExtension[spl] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionExtension[' . $this->extensionName . "] {\n}\n";
    }

    /**
     * returns a list of all functions
     *
     * @return  array<stubReflectionFunction>
     */
    public function getFunctions()
    {
        $functions        = parent::getFunctions();
        $stubRefFunctions = array();
        foreach ($functions as $function) {
            $stubRefFunctions[] = new stubReflectionFunction($function->getName());
        }
        
        return $stubRefFunctions;
    }

    /**
     * returns a list of all classes
     *
     * @return  array<stubReflectionClass>
     */
    public function getClasses()
    {
        $classes        = parent::getClasses();
        $stubRefClasses = array();
        foreach ($classes as $class) {
            $stubRefClasses[] = new stubReflectionClass($class->getName());
        }
        
        return $stubRefClasses;
    }
}
?>