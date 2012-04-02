<?php
/**
 * Helper class to access protected items with no public getter method.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @version     $Id: DefaultProcessorResolverPropertyAccessor.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubDefaultProcessorResolver');
/**
 * Helper class to access protected items with no public getter method.
 *
 * @package     stubbles
 * @subpackage  websites_processors_test
 * @static
 * @deprecated
 */
class DefaultProcessorResolverPropertyAccessor extends stubDefaultProcessorResolver
{
    /**
     * returns the default processor param value
     *
     * @param   stubDefaultProcessorResolver  $defaultProcessorResolver
     * @return  string
     */
    public static function getDefaultProcessorParamValue(stubDefaultProcessorResolver $defaultProcessorResolver)
    {
        return $defaultProcessorResolver->defaultProcessorParamValue;
    }

    /**
     * returns list of configured processor classes with their param value
     *
     * @param   stubDefaultProcessorResolver  $defaultProcessorResolver
     * @return  array<string,string>
     */
    public static function getProcessors(stubDefaultProcessorResolver $defaultProcessorResolver)
    {
        return $defaultProcessorResolver->processors;
    }

    /**
     * returns list of interceptor descriptors
     *
     * @param   stubDefaultProcessorResolver  $defaultProcessorResolver
     * @return  array<string,string>
     */
    public static function getInterceptorDescriptors(stubDefaultProcessorResolver $defaultProcessorResolver)
    {
        return $defaultProcessorResolver->interceptorDescriptors;
    }
}
?>