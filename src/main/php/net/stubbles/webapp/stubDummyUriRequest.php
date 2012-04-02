<?php
/**
 * Uri request class for backward compatibility in 1.7.0, used as marker only.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
/**
 * Uri request class for backward compatibility in 1.7.0, used as marker only.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 * @deprecated  for backward compatibility only
 */
class stubDummyUriRequest extends stubUriRequest
{
    /**
     * constructor
     *
     * @param  string  $current
     */
    public function __construct($current)
    {
        parent::__construct($current);
        $this->setProcessorUriCondition('/xml/');
    }

}
?>