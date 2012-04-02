<?php
/**
 * Base class to create string filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractStringFilterAnnotationReader');
/**
 * Base class to create string filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubTextFilterAnnotationReader extends stubAbstractStringFilterAnnotationReader
{
    /**
     * creates filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilterBuilder
     */
    protected function doDoCreateFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation)
    {
        $textFilter = $filterFactory->createForType('text');
        if ($filterAnnotation->hasAllowedTags() === true) {
            $textFilter->setAllowedTags(array_map('trim', explode(',', $filterAnnotation->getAllowedTags())));
        }

        return $textFilter;
    }
}
?>