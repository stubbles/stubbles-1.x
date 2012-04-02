<?php
/**
 * Provider to create a xml stream writer instances.
 *
 * @package     stubbles
 * @subpackage  xml
 * @version     $Id: stubXmlStreamWriterProvider.php 2364 2009-10-29 17:35:20Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::xml::stubXMLException'
);
/**
 * Provider to create a xml stream writer instances.
 *
 * @package     stubbles
 * @subpackage  xml
 * @since       1.1.0
 */
class stubXmlStreamWriterProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * list of available streamwriter types
     *
     * @var  array
     */
    protected $types = array('dom'       => 'Dom',
                             'xmlwriter' => 'LibXml'
                       );
    /**
     * default version of xml stream writers to create
     *
     * @var  string
     */
    protected $version  = '1.0';
    /**
     * default encoding of xml stream writers to create
     *
     * @var  string
     */
    protected $encoding = 'UTF-8';

    /**
     * set available xml stream writer types
     *
     * @param  array<string,string>  $types
     * @Inject(optional=true)
     * @Named('net.stubbles.xml.types')
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    /**
     * sets the default version of xml stream writers to create
     *
     * @param  string  $version
     * @Inject(optional=true)
     * @Named('net.stubbles.xml.version')
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * sets the default encoding of xml stream writers to create
     *
     * @param  string  $encoding
     * @Inject(optional=true)
     * @Named('net.stubbles.xml.encoding')
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        if (null != $name) {
            return $this->createStreamWriter($name);
        }
        
        return $this->createAsAvailable();
    }

    /**
     * creates a xml stream writer of the given type
     *
     * @param   string               $xmlExtension  concrete type to create
     * @return  stubXMLStreamWriter
     */
    protected function createStreamWriter($xmlExtension)
    {
        $fqClassName = 'net::stubbles::xml::stub' . $this->types[$xmlExtension] . 'XMLStreamWriter';
        $nqClassName = stubClassLoader::getNonQualifiedClassName($fqClassName);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($fqClassName);
        }

        return new $nqClassName($this->version, $this->encoding);
    }

    /**
     * creates a xml stream writer depending on available xml extensions
     *
     * @return  stubXMLStreamWriter
     * @throws  stubXMLException
     */
    protected function createAsAvailable()
    {
        foreach (array_keys($this->types) as $xmlExtension) {
            if (extension_loaded($xmlExtension) === true) {
                return $this->createStreamWriter($xmlExtension);
            }
        }

        throw new stubXMLException('No supported xml extension available, can not create a xml stream writer!');
    }
}
?>