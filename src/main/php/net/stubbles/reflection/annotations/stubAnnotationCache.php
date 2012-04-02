<?php
/**
 * Static cache for annotations
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 * @version     $Id: stubAnnotationCache.php 3220 2011-11-14 15:33:46Z mikey $
 */
/**
 * Static cache for annotations
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 * @static
 */
class stubAnnotationCache
{
    /**
     * Property to store annotations
     *
     * @var  array<string,array>
     */
    private static $annotations  = array(stubAnnotation::TARGET_CLASS    => array(),
                                         stubAnnotation::TARGET_FUNCTION => array(),
                                         stubAnnotation::TARGET_METHOD   => array(),
                                         stubAnnotation::TARGET_PROPERTY => array()
                                   );
    /**
     * flag whether cache contents changed
     *
     * @var  bool
     */
    private static $cacheChanged = false;
    /**
     * file where annotation cache should be stored
     *
     * @var  string
     */
    private static $cacheFile;

    /**
     * sets cache file to be used
     *
     * @param  string  $cacheFile
     */
    public static function setCacheFile($cacheFile)
    {
        self::$cacheFile = $cacheFile;
    }

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$cacheFile = stubBootstrap::getCurrentProjectPath() . '/cache/annotations.cache';
        if (file_exists(self::$cacheFile) == true) {
            self::$annotations = unserialize(file_get_contents(self::$cacheFile));
        }
        
        register_shutdown_function(array(__CLASS__, '__shutdown'));
    }

    /**
     * static shutdown
     */
    public static function __shutdown()
    {
        if (true === self::$cacheChanged) {
            file_put_contents(self::$cacheFile, serialize(self::$annotations));
        }
    }

    /**
     * refreshes cache data
     */
    public static function refresh()
    {
        file_put_contents(self::$cacheFile, serialize(self::$annotations));
        self::$annotations  = unserialize(file_get_contents(self::$cacheFile));
        self::$cacheChanged = false;
    }

    /**
     * flushes all contents from cache
     */
    public static function flush()
    {
        self::$annotations  = array(stubAnnotation::TARGET_CLASS    => array(),
                                    stubAnnotation::TARGET_FUNCTION => array(),
                                    stubAnnotation::TARGET_METHOD   => array(),
                                    stubAnnotation::TARGET_PROPERTY => array()
                              );
        self::$cacheChanged = true;
    }

    /**
     * store an annotation in the cache
     *
     * @param  int             $target          target of the annotation
     * @param  string          $targetName      name of the target
     * @param  string          $annotationName  name of the annotation
     * @param  stubAnnotation  $annotation      optional  the annotation to store
     */
    public static function put($target, $targetName, $annotationName, stubAnnotation $annotation = null)
    {
        if (isset(self::$annotations[$target][$targetName]) === false) {
            self::$annotations[$target][$targetName] = array();
        }
        
        if (null !== $annotation) {
            $clone = clone $annotation;
            self::$annotations[$target][$targetName][$annotationName] = $clone->getSerialized();
        } else {
            self::$annotations[$target][$targetName][$annotationName] = '';
        }
        
        self::$cacheChanged = true;
    }

    /**
     * removes an annotation from the cache
     *
     * @param  int             $target          target of the annotation
     * @param  string          $targetName      name of the target
     * @param  string          $annotationName  name of the annotation
     */
    public static function remove($target, $targetName, $annotationName)
    {
        if (isset(self::$annotations[$target][$targetName]) === false || isset(self::$annotations[$target][$targetName][$annotationName]) === false) {
            return;
        }
        
        unset(self::$annotations[$target][$targetName][$annotationName]);
        self::$cacheChanged = true;
    }

    /**
     * check, whether an annotation is available in the cache
     *
     * @param   int     $target          target of the annotation
     * @param   string  $targetName      name of the target
     * @param   string  $annotationName  name of the annotation
     * @return  bool
     */
    public static function has($target, $targetName, $annotationName)
    {
        if (isset(self::$annotations[$target][$targetName]) === false) {
            return false;
        }
        
        if (isset(self::$annotations[$target][$targetName][$annotationName]) === false) {
            return false;
        }
        
        return self::$annotations[$target][$targetName][$annotationName] !== '';
    }

    /**
     * check, whether an annotation is available in the cache
     *
     * @param   int     $target          target of the annotation
     * @param   string  $targetName      name of the target
     * @param   string  $annotationName  name of the annotation
     * @return  bool
     */
    public static function hasNot($target, $targetName, $annotationName)
    {
        if (isset(self::$annotations[$target][$targetName]) == false) {
            return false;
        }
        
        if (isset(self::$annotations[$target][$targetName][$annotationName]) == false) {
            return false;
        }
        
        return self::$annotations[$target][$targetName][$annotationName] === '';
    }

    /**
     * fetch an annotation from the cache
     *
     * @param   int             $target          target of the annotation
     * @param   string          $targetName      name of the target
     * @param   string          $annotationName  name of the annotation
     * @return  stubAnnotation
     */
    public static function get($target, $targetName, $annotationName)
    {
        if (self::has($target, $targetName, $annotationName) === true) {
            return clone self::$annotations[$target][$targetName][$annotationName]->getUnserialized();
        }
        
        return null;
    }
}
?>