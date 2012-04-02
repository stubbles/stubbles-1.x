<?php
/**
 * Interface for rss feeds to be accessed via the rss processor.
 *
 * @package     stubbles
 * @subpackage  xml_rss
 */
stubClassLoader::load('net::stubbles::xml::rss::stubRSSFeedGenerator');
/**
 * Interface for rss feeds to be accessed via the rss processor.
 *
 * @package     stubbles
 * @subpackage  xml_rss
 */
interface stubRSSFeed extends stubObject
{
    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable();

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars();

    /**
     * creates the rss feed
     *
     * @return  stubRSSFeedGenerator
     */
    public function create();
}
?>