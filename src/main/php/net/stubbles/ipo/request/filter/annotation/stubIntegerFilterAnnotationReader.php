<?php
/**
 * Class to create integer filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader');
/**
 * Class to create integer filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubIntegerFilterAnnotationReader extends stubAbstractFilterAnnotationReader
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
        $intFilter = $filterFactory->createForType('int');
        if ($filterAnnotation->hasMinValue() === true  || $filterAnnotation->hasMaxValue() === true) {
            $intFilter->inRange($filterAnnotation->getMinValue(),
                                $filterAnnotation->getMaxValue(),
                                $filterAnnotation->getMinErrorId(),
                                $filterAnnotation->getMaxErrorId());
        }

        return $intFilter;
    }
}
?>