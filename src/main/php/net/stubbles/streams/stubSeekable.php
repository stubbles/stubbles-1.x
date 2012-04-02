<?php
/**
 * A seekable stream may be altered in its position to read data.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubSeekable.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * A seekable stream may be altered in its position to read data.
 *
 * @package     stubbles
 * @subpackage  streams
 */
interface stubSeekable extends stubObject
{
    /**
     * set position equal to offset  bytes
     */
    const SET     = SEEK_SET;
    /**
     * set position to current location plus offset
     */
    const CURRENT = SEEK_CUR;
    /**
     * set position to end-of-file plus offset
     */
    const END     = SEEK_END;

    /**
     * seek to given offset
     *
     * @param  int  $offset
     * @param  int  $whence  one of stubSeekable::SET, stubSeekable::CURRENT or stubSeekable::END
     */
    public function seek($offset, $whence = stubSeekable::SET);

    /**
     * return current position
     *
     * @return  int
     */
    public function tell();
}
?>