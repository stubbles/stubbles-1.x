<?php
/**
 * Creates proper formatter instances based on annotations and mime type accept header.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @version     $Id: stubFormatFactory.php 3204 2011-11-02 16:12:02Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::lang::stubProperties',
                      'net::stubbles::peer::http::stubAcceptHeader',
                      'net::stubbles::reflection::stubReflectionMethod',
                      'net::stubbles::service::rest::format::stubFormatter',
                      'net::stubbles::service::rest::format::stubErrorFormatter',
                      'net::stubbles::service::rest::format::stubVoidFormatter'
);
/**
 * Creates proper formatter instances based on annotations and mime type accept header.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @since       1.7.0
 */
class stubFormatFactory extends stubBaseObject
{
    /**
     * injector instance to create rest handler instances
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * rest configuration
     *
     * @var  stubProperties
     */
    protected $restProperties;
    /**
     * default formatter type
     */
    const TYPE_DEFAULT        = 'formatter';
    /**
     * error formatter type
     */
    const TYPE_ERROR          = 'errorFormatter';

    /**
     * constructor
     *
     * @param   stubInjector            $injector    injector instance to create rest handler instances
     * @param   string                  $configPath  path to config files
     * @throws  stubProcessorException
     * @Inject
     * @Named{configPath}('net.stubbles.config.path')
     */
    public function __construct(stubInjector $injector,
                                $configPath)
    {
        $this->injector       = $injector;
        $this->restProperties = stubProperties::fromFile($configPath . '/rest.ini');
    }

    /**
     * creates formatter based on different sources
     *
     * Formatter is selected based on the following order:
     * 1. Formatter class defined within the RestMethod annotation of the method
     *    to call.
     * 2. First match of Accept header.
     * 3. First configured formatter from rest.ini.
     *
     * @param   stubAcceptHeader      $acceptedMimeTypes
     * @param   stubReflectionMethod  $method             called method
     * @return  stubFormatter
     */
    public function createFormatter(stubAcceptHeader $acceptedMimeTypes, stubReflectionMethod $method)
    {
        return $this->getFormatterInComplianceWithAcceptedMimeTypes(self::TYPE_DEFAULT,
                                                                    $acceptedMimeTypes,
                                                                    $this->getFormatterForMethod(self::TYPE_DEFAULT, $method)
        );
    }

    /**
     * creates error formatter based on different sources
     *
     * Formatter is selected based on the following order:
     * 1. Error formatter class defined within the RestMethod annotation of the
     *    method to call.
     * 2. First match of Accept header.
     * 3. First configured error formatter from rest.ini.
     *
     * @param   stubAcceptHeader      $acceptedMimeTypes
     * @param   stubReflectionMethod  $method             called method
     * @return  stubErrorFormatter
     */
    public function createErrorFormatter(stubAcceptHeader $acceptedMimeTypes, stubReflectionMethod $method = null)
    {
        return $this->getFormatterInComplianceWithAcceptedMimeTypes(self::TYPE_ERROR,
                                                                    $acceptedMimeTypes,
                                                                    $this->getFormatterForMethod(self::TYPE_ERROR, $method)
        );
    }

    /**
     * returns list of supported mime types for given method
     *
     * @param   stubReflectionMethod  $method
     * @return  array<string>
     */
    public function getSupportedMimeTypes(stubReflectionMethod $method)
    {
        return $this->getMimeTypes(self::TYPE_DEFAULT, $method);
    }

    /**
     * returns list of supported mime types for error messages
     *
     * @param   stubReflectionMethod  $method  optional
     * @return  array<string>
     */
    public function getSupportedErrorMimeTypes(stubReflectionMethod $method = null)
    {
        return $this->getMimeTypes(self::TYPE_ERROR, $method);
    }

    /**
     * returns list of allowed mime types for given method and format type
     *
     * @param   string                $type    stubFormatFactory::TYPE_DEFAULT or stubFormatFactory::TYPE_ERROR
     * @param   stubReflectionMethod  $method  called method
     * @return  array<string>
     */
    protected function getMimeTypes($type, stubReflectionMethod $method = null)
    {
        $supportedMimeTypes = $this->restProperties->getSectionKeys($type);
        $wishedFormatter    = $this->getFormatterForMethod($type, $method);
        if (null !== $wishedFormatter) {
            $supportedMimeTypes[] = $wishedFormatter->getContentType();
        }

        return $supportedMimeTypes;
    }

    /**
     * returns formatter annotated at method
     *
     * Returns null if no formatter is annotated
     * @param   string                            $type    stubFormatFactory::TYPE_DEFAULT or stubFormatFactory::TYPE_ERROR
     * @param   stubReflectionMethod              $method  called method
     * @return  stubFormatter|stubErrorFormatter
     */
    protected function getFormatterForMethod($type, stubReflectionMethod $method = null)
    {
        if (null !== $method && $method->hasAnnotation('RestMethod') === true) {
            $restMethodAnnotation = $method->getAnnotation('RestMethod');
            if ($restMethodAnnotation->hasValueByName($type) === true) {
                return $this->injector->getInstance($restMethodAnnotation->getValueByName($type)
                                                                         ->getFullQualifiedClassName()
                );
            }
        }

        return null;
    }

    /**
     * tries to return formatter in accordance with accept header
     *
     * @param   string                            $type               stubFormatFactory::TYPE_DEFAULT or stubFormatFactory::TYPE_ERROR
     * @param   stubAcceptHeader                  $acceptedMimeTypes
     * @param   stubFormatContentType             $wishedFormatter    optional
     * @return  stubFormatter|stubErrorFormatter
     */
    protected function getFormatterInComplianceWithAcceptedMimeTypes($type, stubAcceptHeader $acceptedMimeTypes, stubFormatContentType $wishedFormatter = null)
    {
        if ($this->isWishedFormatterAcceptable($acceptedMimeTypes, $wishedFormatter) === true) {
            return $wishedFormatter;
        }

        if (count($acceptedMimeTypes) === 0) {
            return $this->injector->getInstance(array_shift($this->restProperties->getSection($type)));
        }

        if ($acceptedMimeTypes->hasSharedAcceptables($this->restProperties->getSectionKeys($type)) === true) {
            return $this->injector->getInstance($this->restProperties->getValue($type,
                                                                            $acceptedMimeTypes->findMatchWithGreatestPriority($this->restProperties->getSectionKeys($type))
                                                                   )
            );
        }

        if ($this->restProperties->hasValue($type, '*/*') === true) {
            return new stubVoidFormatter();
        }

        return null;

    }

    /**
     * checks whether wished formatter is acceptable based on given mime types
     *
     * @param   stubAcceptHeader       $acceptedMimeTypes
     * @param   stubFormatContentType  $wishedFormatter
     * @return  bool
     */
    protected function isWishedFormatterAcceptable(stubAcceptHeader $acceptedMimeTypes, stubFormatContentType $wishedFormatter = null)
    {
        if (null !== $wishedFormatter && ($acceptedMimeTypes->priorityFor($wishedFormatter->getContentType()) > 0 || ($wishedFormatter instanceof stubVoidFormatter))) {
            return true;
        }

        return false;
    }
}
?>