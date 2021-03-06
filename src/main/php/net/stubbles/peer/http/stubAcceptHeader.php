<?php
/**
 * Class to work with all kinds of Accept* headers.
 *
 * @package     stubbles
 * @subpackage  peer_http
 * @version     $Id: stubAcceptHeader.php 2655 2010-08-19 09:48:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException');
/**
 * Class to work with all kinds of Accept* headers.
 *
 * @package     stubbles
 * @subpackage  peer_http
 */
class stubAcceptHeader extends stubBaseObject implements Countable
{
    /**
     * list of acceptables
     *
     * @var  array<string,float>
     */
    protected $acceptables = array();

    /**
     * method to create an instance from a string header value
     *
     * @param   string            $headerValue
     * @return  stubAcceptHeader
     */
    public static function parse($headerValue)
    {
        $self = new self();
        foreach (explode(',', $headerValue) as $acceptable) {
            // seems to be impossible to parse acceptables with regular
            // expressions or even scanf(), so we do some string crunching here
            if (strstr($acceptable, 'q=') !== false) {
                list($acceptable, $priority) = explode('q=', trim($acceptable));
            } else {
                $priority = 1;
            }

            settype($priority, 'float');
            $acceptable = trim($acceptable);
            if (substr($acceptable, -1) === ';') {
                $acceptable = substr($acceptable, 0, -1);
            }

            $self->addAcceptable($acceptable, $priority);
        }

        return $self;
    }

    /**
     * amount of acceptables
     *
     * @return  int
     */
    public function count()
    {
        return count($this->acceptables);
    }

    /**
     * add an acceptable to the list
     *
     * @param   string            $acceptable
     * @param   float             $priority    optional  defaults to 1.0
     * @return  stubAcceptHeader
     * @throws  stubIllegalArgumentException
     */
    public function addAcceptable($acceptable, $priority = 1.0)
    {
        if (0 > $priority || 1.0 < $priority) {
            throw new stubIllegalArgumentException('Invalid priority, must be between 0 and 1.0');
        }

        $this->acceptables[$acceptable] = $priority;
        return $this;
    }

    /**
     * returns current list of acceptables
     *
     * @return  array<string,float>
     */
    public function getList()
    {
        return $this->acceptables;
    }

    /**
     * returns priority for given acceptable
     *
     * If returned priority is 0 the requested acceptable is not in the list. In
     * case no acceptables were added before every requested acceptable has a
     * priority of 1.0.
     *
     * @param   string  $acceptable
     * @return  float
     */
    public function priorityFor($acceptable)
    {
        if (isset($this->acceptables[$acceptable]) === false) {
            if ($this->count() === 0) {
                return 1.0;
            } elseif (isset($this->acceptables['*/*']) === true) {
                return $this->acceptables['*/*'];
            }

            list($maintype, $subtype) = explode('/', $acceptable);
            if (isset($this->acceptables[$maintype . '/*']) === true) {
                return $this->acceptables[$maintype . '/*'];
            }
            
            return 0;
        }

        return $this->acceptables[$acceptable];
    }

    /**
     * find match with highest priority
     *
     * Checks given list of acceptables if they are in the list, and returns the
     * one with the greatest priority. If return value is null none of the given
     * acceptables matches any in the list.
     *
     * @param   array<string>  $acceptables
     * @return  string
     */
    public function findMatchWithGreatestPriority(array $acceptables)
    {
        $sharedAcceptables = array_intersect_key($this->acceptables,
                                                 array_flip($this->getSharedAcceptables($acceptables))
        );
        if (count($sharedAcceptables) > 0) {
            return $this->findAcceptableWithGreatestPriorityFromList($sharedAcceptables);
        }

        foreach ($acceptables as $acceptable) {
            list($maintype, $subtype) = explode('/', $acceptable);
            if (isset($this->acceptables[$maintype . '/*']) === true) {
                return $acceptable;
            }
        }

        if (isset($this->acceptables['*/*']) === true) {
            return array_shift($acceptables);
        }
        
        return null;
    }

    /**
     * helper method to find the acceptable with the greatest priority from a given list of acceptables
     *
     * @param   array<string,float>  $acceptables
     * @return  string
     */
    protected function findAcceptableWithGreatestPriorityFromList(array $acceptables)
    {
        if (count($acceptables) === 0) {
            return null;
        }
        
        arsort($acceptables);
        // use temp var to prevent E_STRICT Only variables should be passed by reference
        $acceptableKeys = array_keys($acceptables);
        return array_shift($acceptableKeys);
    }

    /**
     * returns the acceptable with the greatest priority
     *
     * If two acceptables have the same priority the last one added wins.
     *
     * @return  string
     */
    public function findAcceptableWithGreatestPriority()
    {
        return $this->findAcceptableWithGreatestPriorityFromList($this->acceptables);
    }

    /**
     * checks whether there are shares acceptables in header and given list
     *
     * @param   array<string>  $acceptables
     * @return  bool
     */
    public function hasSharedAcceptables(array $acceptables)
    {
        return (count($this->getSharedAcceptables($acceptables)) > 0);
    }

    /**
     * returns a list of acceptables that are both in header and given list
     *
     * @param   array<string>  $acceptables
     * @return  array<string>
     */
    public function getSharedAcceptables(array $acceptables)
    {
        return array_intersect(array_keys($this->acceptables), $acceptables);
    }

    /**
     * returns current list as string
     *
     * @return  string
     */
    public function asString()
    {
        $parts = array();
        foreach ($this->acceptables as $acceptable => $priority) {
            if (1.0 === $priority) {
                $parts[] = $acceptable;
            } else {
                $parts[] = $acceptable . ';q=' . $priority;
            }
        }

        return join(',', $parts);
    }
}
?>