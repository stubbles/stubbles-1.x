<?php
/**
 * Base class for the different variant types.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 * @version     $Id: stubAbstractVariant.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubConfigurableVariant',
                      'net::stubbles::webapp::variantmanager::stubVariantConfigurationException'
);
/**
 * Base class for the different variant types.
 * 
 * Implements functionality that is shared and does not influence variant
 * selection algorithms.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 */
abstract class stubAbstractVariant extends stubSerializableObject implements stubConfigurableVariant
{
    /**
     * List of all children of the variant
     * 
     * @var  array<stubVariant>
     */
    protected $children = array();
    /**
     * Reference to the parent variant.
     * 
     * @var  stubVariant
     */
    protected $parent;
    /**
     * Name of the variant
     * 
     * @var  string
     */
    protected $name     = '';
    /**
     * Title of the variant, only used when exporting the variant configuration
     * 
     * @var  string
     */
    protected $title    = '';
    /**
     * alias of the variant, only used when exporting the variant configuration
     * 
     * @var  string
     */
    protected $alias    = '';

    /**
     * sets the name of the variant
     *
     * @param   string                   $name
     * @return  stubConfigurableVariant
     * @throws  stubVariantConfigurationException
     */
    public function setName($name)
    {
        if (strlen($name) > 12) {
            throw new stubVariantConfigurationException("The variant name '" . $name . "' is too long. Variant names must not be longer than 12 characters.");
        }

        $this->name = $name;
        return $this;
    }

    /**
     * returns the name of the variant
     * 
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * returns the full qualified name of the variant
     *
     * @return  string
     */
    public function getFullQualifiedName()
    {
        if ($this->hasParent() === true && ($this->parent instanceof stubRootVariant) === false) {
            return $this->parent->getFullQualifiedName() . ':' . $this->name;
        }
        
        return $this->name;
    }

    /**
     * sets the title of the variant
     *
     * @param   string                   $title
     * @return  stubConfigurableVariant
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * returns title of the variant
     * 
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * set alias name of the variant
     *
     * @param   string                   $alias
     * @return  stubConfigurableVariant
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    /**
     * returns alias name of the variant
     * 
     * @return  string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * return the forced variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  stubVariant
     */
    public function getEnforcingVariant(stubSession $session, stubRequest $request)
    {
        if ($this->isEnforcing($session, $request) === true) {
            if (count($this->children) > 0) {
                foreach ($this->children as $child) {
                    $validChild = $child->getEnforcingVariant($session, $request);
                    if (null !== $validChild) {
                        return $validChild;
                    }
                }
            }
            
            return $this;
        }
        
        return null;
    }

    /**
     * return the variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  stubVariant
     */
    public final function getVariant(stubSession $session, stubRequest $request)
    {
        if ($this->isValid($session, $request) === true) {
            if (count($this->children) > 0) {
                foreach ($this->children as $child) {
                    $validChild = $child->getVariant($session, $request);
                    if (null !== $validChild) {
                        return $validChild;
                    }
                }
            }
            
            $this->assign($session, $request);
            return $this;
        }
        
        return null;
    }

    /**
     * check whether the conditions for this variant are met
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function conditionsMet(stubSession $session, stubRequest $request)
    {
        return $this->isValid($session, $request);
    }

    /**
     * assign that this variant has been choosen
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool         true if was assigned, else false
     */
    public function assign(stubSession $session, stubRequest $request)
    {
        if ($this->hasParent() === true) {
            $this->getParent()->assign($session, $request);
            return true;
        }
        
        return false;
    }

    /**
     * add a child variant
     *
     * @param   stubConfigurableVariant  $child
     * @return  stubConfigurableVariant
     * @throws  stubVariantConfigurationException
     */
    public function addChild(stubConfigurableVariant $child)
    {
        if ($child->hashCode() === $this->hashCode()) {
            throw new stubVariantConfigurationException('A variant can not add itself as child.');
        }

        $this->children[$child->getName()] = $child;
        return $child->setParent($this);
    }

    /**
     * return child variants of this variant
     *
     * @return  array<stubVariant>
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * set parent variant
     *
     * @param   stubVariant              $parent
     * @return  stubConfigurableVariant
     */
    public function setParent(stubVariant $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * check whether the variant has a parent variant
     *
     * @return  bool
     */
    public function hasParent()
    {
        return (null !== $this->parent);
    }

    /**
     * returns parent variant
     * 
     * @return  stubVariant
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * template method to hook into __sleep()
     *
     * @return  array<string>  list of property names that should not be serialized
     */
    protected function __doSleep()
    {
        foreach ($this->children as $name => $child) {
            $this->_serializedProperties[$name] = $child->getSerialized();
        }
        
        return array('children', 'parent');
    }

    /**
     * template method to hook into __wakeup()
     */
    protected function __doWakeUp()
    {
        foreach ($this->_serializedProperties as $serializedChild) {
            $this->addChild($serializedChild->getUnserialized());
        }
        
        $this->_serializedProperties = array();
    }
}
?>