<?php
/**
 * Base class to create string filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractStringFilterAnnotationReader',
                      'net::stubbles::ipo::request::validator::stubRegexValidator'
);
/**
 * Base class to create string filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubStringFilterAnnotationReader extends stubAbstractStringFilterAnnotationReader
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
        $stringFilter = $filterFactory->createForType('string');
        if ($filterAnnotation->hasRegex() === true) {
            $stringFilter->validatedBy(new stubRegexValidator($filterAnnotation->getRegex()),
                                       $filterAnnotation->getRegexErrorId()
            );
        }

        return $stringFilter;
    }
}
?>