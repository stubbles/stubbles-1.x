<?php
/**
 * Helper class to access protected items with no public getter method.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @version     $Id: SimpleProcessorResolverPropertyAccessor.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubSimpleProcessorResolver');
/**
 * Helper class to access protected items with no public getter method.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @static
 * @deprecated
 */
class SimpleProcessorResolverPropertyAccessor extends stubSimpleProcessorResolver
{
    /**
     * returns the default processor param value
     *
     * @param   stubSimpleProcessorResolver  $simpleProcessorResolver
     * @return  string
     */
    public static function getProcessorClassName(stubSimpleProcessorResolver $simpleProcessorResolver)
    {
        return $simpleProcessorResolver->processorClassName;
    }
}
?>