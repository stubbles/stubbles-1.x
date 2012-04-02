<?php
/**
 * Class to create float filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader');
/**
 * Class to create float filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubFloatFilterAnnotationReader extends stubAbstractFilterAnnotationReader
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
        $floatFilter = $filterFactory->createForType('float');
        $floatFilter->setDecimals($filterAnnotation->getDecimals());
        if ($filterAnnotation->hasMinValue() === true  || $filterAnnotation->hasMaxValue() === true) {
            $floatFilter->inRange($filterAnnotation->getMinValue(),
                                  $filterAnnotation->getMaxValue(),
                                  $filterAnnotation->getMinErrorId(),
                                  $filterAnnotation->getMaxErrorId());
        }

        return $floatFilter;
    }
}
?>