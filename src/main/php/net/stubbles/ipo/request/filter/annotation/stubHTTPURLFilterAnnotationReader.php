<?php
/**
 * Class to create http url filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader');
/**
 * Class to create http url filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubHTTPURLFilterAnnotationReader extends stubAbstractFilterAnnotationReader
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
         $httpUrlFilter = $filterFactory->createForType('http');
         // can not chain methods, setCheckDNS() returns the real filter instance
         // which causes the filter builder to return this instance instead of
         // itself
         $httpUrlFilter->setCheckDNS($filterAnnotation->isCheckDNS());
         return $httpUrlFilter;
    }
}
?>