<?php
/**
 * Class to create date filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAbstractFilterAnnotationReader',
                      'net::stubbles::lang::types::stubDate',
                      'net::stubbles::reflection::stubBaseReflectionClass'
);
/**
 * Class to create date filter instances based on data from annotations.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation
 * @Singleton
 */
class stubDateFilterAnnotationReader extends stubAbstractFilterAnnotationReader
{
    /**
     * injector to create class which delivers the list of allowed preselect values
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @Inject
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * creates filter from given annotation
     *
     * @param   stubFilterFactory  $filterFactory
     * @param   stubAnnotation     $filterAnnotation
     * @return  stubFilterBuilder
     */
    protected function doCreateFilter(stubFilterFactory $filterFactory, stubAnnotation $filterAnnotation)
    {
        $minDate    = $this->getMinDate($filterAnnotation);
        $maxDate    = $this->getMaxDate($filterAnnotation);
        $dateFilter = $filterFactory->createForType('date');
        if (null !== $minDate || null !== $maxDate) {
            $dateFilter->inPeriod($minDate,
                                  $maxDate,
                                  $filterAnnotation->getMinDateErrorId(),
                                  $filterAnnotation->getMaxDateErrorId(),
                                  $filterAnnotation->getDateFormat());
        }

        return $dateFilter;
    }

    /**
     * reads minimum date
     *
     * @param   stubAnnotation  $filterAnnotation
     * @return  stubDate
     */
    protected function getMinDate(stubAnnotation $filterAnnotation)
    {
        $minDate = $this->getMinDateValue($filterAnnotation);
        if (null == $minDate) {
            return null;
        }

        if ($minDate instanceof stubDate) {
            return $minDate;
        }

        return new stubDate($minDate);
    }

    /**
     * reads maximum date
     *
     * @param   stubAnnotation  $filterAnnotation
     * @return  stubDate
     */
    protected function getMaxDate(stubAnnotation $filterAnnotation)
    {
        $maxDate = $this->getMaxDateValue($filterAnnotation);
        if (null == $maxDate) {
            return null;
        }

        if ($maxDate instanceof stubDate) {
            return $maxDate;
        }

        return new stubDate($maxDate);
    }

    /**
     * reads value of minimum date
     *
     * @param   stubAnnotation  $filterAnnotation
     * @return  int|string
     */
    protected function getMinDateValue(stubAnnotation $filterAnnotation)
    {
        if ($filterAnnotation->hasMinDateProviderClass() === true) {
            return $this->getDateFromProvider($filterAnnotation->getMinDateProviderClass(), $filterAnnotation->getMinDateProviderMethod());
        }

        return $filterAnnotation->getMinDate();
    }

    /**
     * reads value of maximum date
     *
     * @param   stubAnnotation  $filterAnnotation
     * @return  int|string
     */
    protected function getMaxDateValue(stubAnnotation $filterAnnotation)
    {
        if ($filterAnnotation->hasMaxDateProviderClass() === true) {
            return $this->getDateFromProvider($filterAnnotation->getMaxDateProviderClass(), $filterAnnotation->getMaxDateProviderMethod());
        }

        return $filterAnnotation->getMaxDate();
    }

    /**
     * retrieves date from provider
     *
     * @param   stubBaseReflectionClass  $providerClass
     * @param   string                   $providerMethodName
     * @return  int|string|stubDate
     */
    protected function getDateFromProvider(stubBaseReflectionClass $providerClass, $providerMethodName)
    {
        $method = $providerClass->getMethod($providerMethodName);
        if ($method->isStatic() === true) {
            return $method->invoke(null);
        }

        return $method->invoke($this->injector->getInstance($providerClass->getFullQualifiedClassName()));
    }
}
?>