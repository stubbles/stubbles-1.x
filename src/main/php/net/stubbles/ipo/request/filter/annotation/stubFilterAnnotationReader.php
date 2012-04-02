<?php
/**
 * Interface to create single filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilterFactory',
                      'net::stubbles::reflection::annotations::stubAnnotation'
);
/**
 * Interface to create single filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 */
interface stubFilterAnnotationReader extends stubObject
{
    /**
     * creates filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilter
     */
    public function createFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation);
}
?>