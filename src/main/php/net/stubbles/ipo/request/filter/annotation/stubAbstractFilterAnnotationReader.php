<?php
/**
 * Base class to create filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubFilterAnnotationReader');
/**
 * Base class to create filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 */
abstract class stubAbstractFilterAnnotationReader extends stubBaseObject implements stubFilterAnnotationReader
{
    /**
     * creates filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilter
     */
    public function createFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation)
    {
        $filter = $this->doCreateFilter($filterFactory, $filterAnnotation);
        if ($filterAnnotation->hasRequired() === false || $filterAnnotation->isRequired() === true) {
            $filter->asRequired();
        }

        if ($filterAnnotation->hasDefaultValue() === true && $filterAnnotation->getDefaultValue() !== null) {
            $filter->defaultsTo($filterAnnotation->getDefaultValue());
        }

        return $filter;
    }

    /**
     * creates concrete filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilterBuilder
     */
    protected abstract function doCreateFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation);
}
?>