<?php
/**
 * Variant factory which reads variant configuration from an xml file.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubXmlVariantFactory.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubProperties',
                      'net::stubbles::lang::stubResourceLoader',
                      'net::stubbles::lang::exceptions::stubFileNotFoundException',
                      'net::stubbles::webapp::variantmanager::stubAbstractVariantFactory'
);
/**
 * Variant factory which reads variant configuration from an xml file.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @since       1.6.0
 */
class stubXmlVariantFactory extends stubAbstractVariantFactory
{
    /**
     * list of built-in variants with their parameters
     *
     * @var  array<string,array<string,string>>
     */
    protected static $builtin = array('requestParam' => array('class'      => 'net::stubbles::webapp::variantmanager::types::stubRequestParamVariant',
                                                              'paramName'  => 'required',
                                                              'paramValue' => 'optional'
                                                        ),
                                      'random'       => array('class'      => 'net::stubbles::webapp::variantmanager::types::stubRandomVariant',
                                                              'weight'     => 'required'
                                                        ),
                                      'lead'         => array('class'      => 'net::stubbles::webapp::variantmanager::types::stubLeadVariant')
                                );
    /**
     * loader for master.xsl resource file
     *
     * @var  stubResourceLoader
     */
    protected $resourceLoader;
    /**
     * path to config files
     *
     * @var  string
     */
    protected $configPath;
    /**
     * list of possible variant tags
     *
     * @var  stubProperties
     */
    protected $variantTags;

    /**
     * constructor
     *
     * @param  stubResourceLoader  $resourceLoader
     * @param  string              $configPath
     * @Inject
     * @Named{configPath}('net.stubbles.config.path')
     */
    public function  __construct(stubResourceLoader $resourceLoader, $configPath)
    {
        $this->resourceLoader = $resourceLoader;
        $this->configPath     = $configPath;
        $this->variantTags    = new stubProperties(self::$builtin);
    }

    /**
     * creates the variants map
     *
     * @return  stubVariantsMap
     * @throws  stubFileNotFoundException
     * @throws  stubVariantConfigurationException
     */
    protected function createVariantsMap()
    {
        if (file_exists($this->configPath . '/variantmanager.xml') === false) {
            throw new stubFileNotFoundException($this->configPath . '/variantmanager.xml');
        }

        foreach ($this->resourceLoader->getResourceUris('variantmanager/variantmanager.ini') as $variantsConfigFile) {
            $this->variantTags = $this->variantTags->merge(stubProperties::fromFile($variantsConfigFile));
        }

        libxml_use_internal_errors(true);
        $document = new DOMDocument();
        if ($document->load($this->configPath . '/variantmanager.xml') === false) {
            $xmlError = libxml_get_last_error();
            libxml_clear_errors();
            throw new stubVariantConfigurationException($xmlError->message);
        }

        $variantsMap = new stubVariantsMap();
        $variantsMap->setName($document->documentElement->getAttribute('name'));
        if ($document->documentElement->hasAttribute('usePersistence') === true
          && $document->documentElement->getAttribute('usePersistence') === 'false') {
            $variantsMap->setUsePersistence(false);
        }

        $xpath = new DOMXPath($document);
        foreach ($document->documentElement->childNodes as $childNode) {
            if (XML_ELEMENT_NODE === $childNode->nodeType) {
                $variantsMap->addChild($this->processNode($xpath, $childNode));
            }
        }

        libxml_clear_errors();
        return $variantsMap;
    }

    /**
     * processes given node and all of its childs and returns variant tree for given node
     *
     * @param   DOMXPath     $xpath
     * @param   DOMNode      $node
     * @return  stubVariant
     */
    protected function processNode(DOMXPath $xpath, DOMNode $node)
    {
        $variant = $this->createVariantFromNode($xpath, $node);
        foreach ($node->childNodes as $childNode) {
            if (XML_ELEMENT_NODE === $childNode->nodeType) {
                $variant->addChild($this->processNode($xpath, $childNode));
            }
        }

        return $variant;
    }

    /**
     * creates variant instance from given node
     *
     * @param   DOMXPath    $xpath
     * @param   DOMNode     $node
     * @return  stubVariant
     * @throws  stubVariantConfigurationException
     */
    protected function createVariantFromNode(DOMXPath $xpath, DOMNode $node)
    {
        $tagData     = $this->getTagData($node->nodeName);
        $nqClassName = stubClassLoader::getNonQualifiedClassName($tagData['class']);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($tagData['class']);
        }
        
        unset($tagData['class']);
        return $this->fillFromNode($xpath, $node, new $nqClassName(), $tagData);
    }

    /**
     * retrieves tag data for given tag name
     *
     * @param   string                $tagName
     * @return  array<string,string>
     * @throws  stubVariantConfigurationException
     */
    protected function getTagData($tagName)
    {
        if ($this->variantTags->hasSection($tagName) === false) {
            throw new stubVariantConfigurationException('Can not process variant node ' . $tagName . ', found no meta configuration.');
        }

        if ($this->variantTags->hasValue($tagName, 'class') === false) {
            throw new stubVariantConfigurationException('Can not process variant node ' . $tagName . ', found no appropriate class name.');
        }

        return $this->variantTags->getSection($tagName);
    }

    /**
     * fills variant instance from node
     *
     * @param   DomXPath                 $xpath       xpath instance used for retrieval
     * @param   DOMNode                  $node        node which contains variant parameter values
     * @param   stubConfigurableVariant  $variant     variant instance to fill
     * @param   array<string>            $parameters  list of parameters for the variant
     * @return  stubConfigurableVariant
     * @throws  stubVariantConfigurationException
     */
    protected function fillFromNode(DOMXPath $xpath, DOMNode $node, stubConfigurableVariant $variant, array $parameters)
    {
        foreach ($parameters as $name => $requiredOrOptional) {
            $value = $xpath->evaluate('@' . $name, $node);
            if (false === $value) {
                throw new stubVariantConfigurationException('Invalid attribute ' . $name . ' for variant node ' . $node->nodeName);
            }

            if ((null === $value || 0 === $value->length) && 'required' === $requiredOrOptional) {
                throw new stubVariantConfigurationException('Missing required attribute ' . $name . ' for variant node ' . $node->nodeName);
            } elseif (null !== $value && 0 < $value->length) {
                $methodName = 'set' . ucfirst($name);
                $variant->$methodName($value->item(0)->nodeValue);
            }
        }

        return $variant->setTitle($this->getAttributeValue($xpath, $node, 'title'))
                       ->setName($this->getAttributeValue($xpath, $node, 'name'))
                       ->setAlias($this->getAttributeValue($xpath, $node, 'alias'));
    }

    /**
     * returns value of given attribute
     *
     * @param   DOMXPath  $xpath      xpath instance used for retrieval
     * @param   DOMNode   $node       node which contains attribute
     * @param   string    $attribute  name of attribute to retrieve value of
     * @return  string
     */
    protected function getAttributeValue(DOMXPath $xpath, DOMNode $node, $attribute)
    {
        $value = $xpath->evaluate('@' . $attribute, $node);
        if (false === $value || null === $value || 0 === $value->length) {
            return '';
        }

        return $value->item(0)->nodeValue;
    }
}
?>