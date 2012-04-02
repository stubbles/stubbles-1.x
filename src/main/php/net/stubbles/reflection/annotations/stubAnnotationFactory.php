<?php
/**
 * Factory to create annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 * @version     $Id: stubAnnotationFactory.php 3079 2011-03-01 16:12:41Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotation',
                      'net::stubbles::reflection::annotations::stubAnnotationCache',
                      'net::stubbles::reflection::annotations::stubGenericAnnotation'
);
/**
 * Factory to create annotations.
 *
 * @static
 * @package     stubbles
 * @subpackage  reflection_annotations
 */
class stubAnnotationFactory
{
    /**
     * Prefixes that can be prepended to class names
     *
     * @var  array
     */
    private static $prefixes    = array('stub', '');
    /**
     * instance of the annotation parser
     *
     * @var  stubAnnotationStateParser
     */
    private static $parser      = null;
    /**
     * list of annotation data
     *
     * @var  array<string,array<string,array<string,array<string,string>>>>
     */
    private static $annotations = array();

    /**
     * Creates an annotation from the given docblock comment.
     *
     * @param   string          $comment          the docblock comment that contains the annotation data
     * @param   string          $annotationName   name of the annotation to create
     * @param   int             $target           the target for which the annotation should be created
     * @param   string          $targetName       the name of the target (property, class, method or function name)
     * @param   string          $fileName         the file where the target resides
     * @return  stubAnnotation
     * @throws  ReflectionException
     */
    public static function create($comment, $annotationName, $target, $targetName, $fileName)
    {
        if (stubAnnotationCache::has($target, $fileName . '::' . $targetName, $annotationName) === true) {
            return stubAnnotationCache::get($target, $fileName . '::' . $targetName, $annotationName);
        }
        
        if (stubAnnotationCache::hasNot($target, $fileName . '::' . $targetName, $annotationName) === true) {
            throw new ReflectionException('Can not find annotation ' . $annotationName);
        }

        $hash = md5($fileName . $comment . $targetName);
        if (isset(self::$annotations[$hash]) === false) {
            if (null === self::$parser) {
                stubClassLoader::load('net::stubbles::reflection::annotations::parser::stubAnnotationStateParser');
                self::$parser = new stubAnnotationStateParser();
            }
            
            self::$annotations[$hash] = self::$parser->parse($comment);
        }

        if (isset(self::$annotations[$hash][$annotationName]) === false) {
            // put null into cache to save that the annotation does not exist
            stubAnnotationCache::put($target, $fileName . '::' . $targetName, $annotationName);
            throw new ReflectionException('Can not find annotation ' . $annotationName);
        }
        
        $annotationClass = self::findAnnotationClass(self::$annotations[$hash][$annotationName]['type']);
        $annotation      = new $annotationClass();

        if (($annotation instanceof stubAnnotation) === false) {
            throw new ReflectionException('The annotation: ' . $annotationName . ' is not an instance of net::stubbles::reflection::annotations::stubAnnotation.');
        }
        
        if (strpos($annotationName, '#') !== false) {
            $realAnnotationName = substr($annotationName, 0, strpos($annotationName, '#'));
        } else {
            $realAnnotationName = $annotationName;
        }

        if (self::$annotations[$hash][$annotationName]['type'] !== $realAnnotationName) {
            $annotationType = self::findAnnotationClass($realAnnotationName, true);
            if (($annotation instanceof $annotationType) === false) {
                throw new ReflectionException('The annotation: ' . $annotationName . ' is not an instance of ' . $annotationType . '.');
            }
        }
        
        if (self::isApplicable($annotation, $target) === false) {
            throw new ReflectionException('The annotation: ' . $annotationName . ' is not applicable for the given type.');
        }
        
        self::build($annotation, self::$annotations[$hash][$annotationName]['params']);
        $annotation->setAnnotationName(self::$annotations[$hash][$annotationName]['type']);
        stubAnnotationCache::put($target, $fileName . '::' . $targetName, $annotationName, $annotation);
        return $annotation;
    }

    /**
     * Checks whether an annotation is applicable for the given type or not.
     *
     * @param   stubAnnotation  $annotation  the annotation to check
     * @param   int             $target      the type to check if annotation is applicable for
     * @return  bool
     */
    public static function isApplicable(stubAnnotation $annotation, $target)
    {
        return (($annotation->getAnnotationTarget() & $target) !== 0);
    }

    /**
     * builds the annotation by setting its values from given data
     *
     * @param   stubAnnotation  $annotation  the annotation to build
     * @param   array           $data        data for annotation
     * @throws  ReflectionException  in case setting a data value fails
     */
    public static function build(stubAnnotation $annotation, array $data)
    {
        $refClass = new ReflectionClass($annotation);
        foreach ($data as $name => $value) {
            if ($refClass->hasMethod('set' . ucfirst($name)) === true) {
                $refClass->getMethod('set' . ucfirst($name))->invoke($annotation, $value);
            } elseif ($refClass->hasProperty($name) === true) {
                $refClass->getProperty($name)->setValue($annotation, $value);
            } elseif ($annotation instanceof stubGenericAnnotation) {
                $annotation->$name = $value;
            } else {
                throw new ReflectionException('Annotation value for "' . $name . '" can not be set: no public setter and no public property exists with this name.');
            }
        }
        
        $annotation->finish();
    }

    /**
     * Checks whether the given docblock has the requested annotation
     *
     * @param   string  $comment         the docblock comment that contains the annotation data
     * @param   string  $annotationName  name of the annotation to check for
     * @param   int     $target          the target for which the annotation should be created
     * @param   string  $targetName      the name of the target (property, class, method or function name)
     * @param   string  $fileName        the file where the target resides
     * @return  bool
     */
    public static function has($comment, $annotationName, $target, $targetName, $fileName)
    {
        try {
            $annotation = self::create($comment, $annotationName, $target, $targetName, $fileName);
        } catch (ReflectionException $e) {
            $annotation = null;
        }

        return (null != $annotation);
    }

    /**
     * Add a new annotation prefix
     *
     * @param  string  $prefix
     */
    public static function addAnnotationPrefix($prefix)
    {
        self::$prefixes[] = $prefix;
    }

    /**
     * Try to find the annotation class
     *
     * This method checks, whether there is a class named exactly as the annotation class
     * or a method that has one of the prefixes defined in $prefixes and the postfix 'Annotation'.
     *
     * @param   string  $annotationClass
     * @param   bool    $allowInterface
     * @return  string
     * @throws  ReflectionException
     * @see     addAnnotationPrefix()
     */
    private static function findAnnotationClass($annotationClass, $allowInterface = false)
    {
        if (class_exists($annotationClass, false) === true) {
            return $annotationClass;
        }
        
        if (true === $allowInterface && interface_exists($annotationClass, false) === true) {
            return $annotationClass;
        }
        
        $annotationClassname = $annotationClass  . 'Annotation';
        foreach (self::$prefixes as $prefix) {
            if (class_exists($prefix . $annotationClassname) === true) {
                return $prefix . $annotationClassname;
            }
            
            if (true === $allowInterface && interface_exists($prefix . $annotationClassname, false) === true) {
                return $prefix . $annotationClassname;
            }
        }
        
        return 'stubGenericAnnotation';
    }
}
?>