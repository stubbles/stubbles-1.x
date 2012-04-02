<?php
/**
 * Extended Reflection class for classes that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: reflection.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotation',
                      'net::stubbles::reflection::annotations::stubAbstractAnnotation',
                      'net::stubbles::reflection::annotations::stubAnnotationFactory',
                      'net::stubbles::reflection::stubBaseReflectionClass',
                      'net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::stubReflectionExtension',
                      'net::stubbles::reflection::stubReflectionFunction',
                      'net::stubbles::reflection::stubReflectionMethod',
                      'net::stubbles::reflection::stubReflectionObject',
                      'net::stubbles::reflection::stubReflectionPackage',
                      'net::stubbles::reflection::stubReflectionParameter',
                      'net::stubbles::reflection::stubReflectionPrimitive',
                      'net::stubbles::reflection::stubReflectionProperty',
                      'net::stubbles::reflection::stubReflectionType'
);
?>