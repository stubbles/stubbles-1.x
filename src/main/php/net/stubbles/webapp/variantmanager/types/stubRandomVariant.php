<?php
/**
 * A variant chosen randomly based on the weight defined in the configuration.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 * @version     $Id: stubRandomVariant.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubAbstractVariant');
/**
 * A variant chosen randomly based on the weight defined in the configuration.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 */
class stubRandomVariant extends stubAbstractVariant
{
    /**
     * weight of the variant
     * 
     * @var  int
     */
    private $weight       = 0;
    /**
     * the choosen sibling
     *
     * @var  array<string,int>
     */
    public static $random = array();

    /**
     * Get the weight of the variant
     * 
     * @return  int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set the weight of the variant
     * 
     * @param  int  $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
    
    /**
     * check whether the variant is an enforcing variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function isEnforcing(stubSession $session, stubRequest $request)
    {
        return false;
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
        return true;
    }
    
    /**
     * check whether the variant is valid
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function isValid(stubSession $session, stubRequest $request)
    {
        return $this->equals(self::getRandom($this));
    }
    
    /**
     * finds a random variant
     *
     * @param   stubRandomVariant  $actual
     * @return  stubRandomVariant
     */
    protected static function getRandom(self $actual)
    {
        if ($actual->hasParent() == false) {
            return $actual;
        }
        
        $parentHash = $actual->getParent()->hashCode();
        if (isset(self::$random[$parentHash]) == true) {
            return self::$random[$parentHash];
        }
        
        $siblings = $actual->findSiblings();
        if (count($siblings) == 1) {
            self::$random[$parentHash] = $actual;
            return self::$random[$parentHash];
        }
        
        $choice = array();
        foreach ($siblings as $sibling) {
            for ($i = $sibling->weight; $i > 0; $i--) {
                $choice[] = $sibling;
            }
        }
        
        self::$random[$parentHash] = $choice[rand(0, count($choice) - 1)];
        return self::$random[$parentHash];
    }
    
    /**
     * Find the siblings of this variant
     * 
     * @return  array<stubRandomVariant>
     */
    private function findSiblings()
    {
        $siblings = array();
        $parent   = $this->getParent();
        if (null != $parent) {
            $children = $parent->getChildren();
            foreach ($children as $child) {
                if ($child instanceof self) {
                    $siblings[] = $child;
                }
            }
        }
        
        return $siblings;
    }
}
?>