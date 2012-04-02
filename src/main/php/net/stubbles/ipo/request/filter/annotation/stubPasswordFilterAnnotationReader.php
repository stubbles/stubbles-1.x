<?php
/**
 * Class to create password filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader');
/**
 * Class to create password filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @todo        support non-allowed values
 * @Singleton
 */
class stubPasswordFilterAnnotationReader extends stubAbstractFilterAnnotationReader
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
        $passwordFilter = $filterFactory->createForType('password');
        $passwordFilter->length($this->getMinLength($filterAnnotation),
                                null,
                                $filterAnnotation->getMinLengthErrorId()
        );
        if ($filterAnnotation->hasMinDiffChars() === true) {
            $passwordFilter->minDiffChars($filterAnnotation->getMinDiffChars());
        }

        if ($filterAnnotation->hasEncoderClass() === true) {
            $passwordFilter->encodedWith($filterAnnotation->getEncoderClass()->newInstance());
        }

        return $passwordFilter;
    }

    /**
     * returns minimum password length
     *
     * @param   stubAnnotation  $filterAnnotation
     * @return  int
     */
    protected function getMinLength(stubAnnotation $filterAnnotation)
    {
        if ($filterAnnotation->hasMinLength() === true) {
            return $filterAnnotation->getMinLength();
        }

        return 6;
    }
}
?>