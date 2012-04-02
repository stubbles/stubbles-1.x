<?php
/**
 * Base class to create string filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader');
/**
 * Base class to create string filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 */
abstract class stubAbstractStringFilterAnnotationReader extends stubAbstractFilterAnnotationReader
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
        $stringFilter = $this->doDoCreateFilter($filterFactory, $filterAnnotation);
        if ($filterAnnotation->hasMinLength() === true || $filterAnnotation->hasMaxLength() === true) {
            $stringFilter->length($filterAnnotation->getMinLength(),
                                  $filterAnnotation->getMaxLength(),
                                  $filterAnnotation->getMinLengthErrorId(),
                                  $filterAnnotation->getMaxLengthErrorId()
                             );
        }

        if ($filterAnnotation->hasEncoderClass() === true) {
            $stringFilter->encodedWith($filterAnnotation->getEncoderClass()->newInstance());
        } elseif ($filterAnnotation->hasDecoderClass() === true) {
            $stringFilter->decodedWith($filterAnnotation->getDecoderClass()->newInstance());
        }

        return $stringFilter;
    }

    /**
     * creates filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilterBuilder
     */
    protected abstract function doDoCreateFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation);
}
?>