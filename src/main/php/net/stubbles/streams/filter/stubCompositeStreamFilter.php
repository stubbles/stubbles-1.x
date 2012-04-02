<?php
/**
 * Composite to combine a list of stream filters.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 * @version     $Id: stubCompositeStreamFilter.php 2297 2009-08-21 15:22:25Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::filter::stubStreamFilter');
/**
 * Composite to combine a list of stream filters.
 *
 * @package     stubbles
 * @subpackage  streams_filter
 */
class stubCompositeStreamFilter extends stubBaseObject implements stubStreamFilter
{
    /**
     * list of stream filters to apply
     *
     * @var  array<stubStreamFilter>
     */
    protected $streamFilter = array();

    /**
     * add a stream filter
     *
     * @param   stubStreamFilter           $streamFilter
     * @return  stubCompositeStreamFilter
     */
    public function addStreamFilter(stubStreamFilter $streamFilter)
    {
        $this->streamFilter[] = $streamFilter;
        return $this;
    }

    /**
     * Decides whether data should be filtered or not.
     *
     * @param   string  $data
     * @return  bool
     */
    public function shouldFilter($data)
    {
        foreach ($this->streamFilter as $streamFilter) {
            if ($streamFilter->shouldFilter($data) === true) {
                return true;
            }
        }
        
        return false;
    }
}
?>