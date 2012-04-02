<?php
/**
 * Class to create bool filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader');
/**
 * Class to create bool filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubBoolFilterAnnotationReader extends stubAbstractFilterAnnotationReader
{
    /**
     * creates filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilterBuilder
     */
    protected function doCreateFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation)
    {
        return $filterFactory->createForType('bool');
    }
}
?>