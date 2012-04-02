<?php
/**
 * The variants map knows all configured variants and to check validity of a
 * variant and if a variant is enforcing.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubVariantsMap.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubVariant',
                      'net::stubbles::webapp::variantmanager::types::stubRootVariant',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::session::stubSession'
);
/**
 * The variants map knows all configured variants and to check validity of a
 * variant and if a variant is enforcing.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 */
class stubVariantsMap extends stubBaseObject
{
    /**
     * name of the current variant configuration
     * 
     * @var  string
     */
    private $name;
    /**
     * switch whether persistence should be used or not
     * 
     * @var  boolean
     */
    private $usePersistence = true;
    /**
     * Flat version of all variants
     * 
     * @var  array<string,stubVariant>
     */
    private $variants       = array();
    /**
     * tree structure of the variants
     * 
     * @var  stubRootVariant
     */
    protected $root;

    /**
     * constructor
     * 
     * @param  stubRootVariant  $rootVariant  optional
     */
    public function __construct(stubRootVariant $rootVariant = null)
    {
        if (null == $rootVariant) {
            $this->root = new stubRootVariant();
        } else {
            $this->root = $rootVariant;
            $children   = $rootVariant->getChildren();
            foreach ($children as $child) {
                $this->flattenVariantTree($child);
            }
        }
    }

    /**
     * returns a flat list of all variants
     * 
     * @return  array<String,stubVariant>
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * returns a variant by supplying its name
     * 
     * @param   string           $variantName
     * @return  stubVariant
     */
    public function getVariantByName($variantName)
    {
        if (isset($this->variants[$variantName]) == true) {
            return $this->variants[$variantName];
        }
        
        return null;
    }

    /**
     * returns a list of all variants
     * 
     * @return  array<String>
     */
    public function getVariantNames()
    {
        return array_keys($this->variants);
    }

    /**
     * adds a new variant
     * 
     * @param  stubVariant  $child
     */
    public function addChild(stubVariant $child)
    {
        $this->root->addChild($child);
        $this->flattenVariantTree($child);
    }

    /**
     * store a variant and all of its children in the flat variant list
     * 
     * @param  stubVariant  $variant
     */
    private function flattenVariantTree(stubVariant $variant)
    {
        $this->variants[$variant->getName()]              = $variant;
        $this->variants[$variant->getFullQualifiedName()] = $variant;
        $children = $variant->getChildren();
        foreach ($children as $child) {
            $this->flattenVariantTree($child);
        }
    }

    /**
     * returns the name of the current variant configuration
     * 
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * sets the name of the variant configuration
     * 
     * @param  string  $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * checks whether we should use persistence or not
     * 
     * @return  boolean
     */
    public function shouldUsePersistence()
    {
        return $this->usePersistence;
    }

    /**
     * sets whether persistence should be used or not
     * 
     * @param  boolean  $usePersistence  the usePersistence to set
     */
    public function setUsePersistence($usePersistence)
    {
        $this->usePersistence = $usePersistence;
    }

    /**
     * Checks, whether a variant is valid for the current request and the
     * session of the user.
     * 
     * This method also checks, whether all parent variants of the
     * variant are valid, as the conditions should be inherited from
     * the parents.
     * 
     * @param   string       $variantName
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  boolean
     */
    public function isVariantValid($variantName, stubSession $session, stubRequest $request)
    {
        if (isset($this->variants[$variantName]) == false) {
            return false;
        }
        
        $variant = $this->variants[$variantName];
        if ($variant->conditionsMet($session, $request)) {
            while ($variant->hasParent()) {
                $variant = $variant->getParent();
                if ($variant->conditionsMet($session, $request) == false) {
                    return false;
                }
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * returns a variant that enforces to be used based on the session of the user
     * and the current request
     * 
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  stubVariant
     */
    public function getEnforcingVariant(stubSession $session, stubRequest $request)
    {
        $enforcing = $this->root->getEnforcingVariant($session, $request);
        if (null === $enforcing || $enforcing instanceof stubRootVariant) {
            return null;
        }
        
        return $enforcing;
    }

    /**
     * returns the matching variant based on the current request and the session
     * of the user
     * 
     * @param   stubSession  $session
     * @param   stubRequest  $request
     * @return  stubVariant
     * @throws  stubVariantConfigurationException
     */
    public function getVariant(stubSession $session, stubRequest $request)
    {
        $variant = $this->root->getVariant($session, $request);
        if ($variant instanceof stubRootVariant) {
            throw new stubVariantConfigurationException("No valid variant for current session and request found.");
        }
        
        return $variant;
    }

    /**
     * checks, whether a specified variant exists
     * 
     * @param   string   $variantName
     * @return  boolean
     */
    public function variantExists($variantName)
    {
        return isset($this->variants[$variantName]);
    }

    /**
     * returns the root variant
     * 
     * @return  stubRootVariant
     */
    public function getRootVariant()
    {
        return $this->root;
    }
}
?>