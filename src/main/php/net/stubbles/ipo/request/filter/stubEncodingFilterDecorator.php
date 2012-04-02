<?php
/**
 * Class for decoding/encoding values using a string encoder.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubEncodingFilterDecorator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubStrategyFilterDecorator',
                      'net::stubbles::php::string::stubStringEncoder'
);
/**
 * Class for decoding/encoding values using a string encoder.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubEncodingFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * the encoder to be applied on the value to filter
     *
     * @var  stubStringEncoder
     */
    protected $encoder     = null;
    /**
     * the encoding mode to be applied on the value to filter
     *
     * @var  int
     */
    protected $encoderMode = stubStringEncoder::MODE_DECODE;

    /**
     * constructor
     *
     * @param  stubFilter         $filter   decorated filter
     * @param  stubStringEncoder  $encoder  encoder to be used
     * @param  int                $mode     optional
     */
    public function __construct(stubFilter $filter, stubStringEncoder $encoder, $mode = stubStringEncoder::MODE_DECODE)
    {
        $this->setDecoratedFilter($filter);
        $this->encoder     = $encoder;
        $this->encoderMode = $mode;
    }

    /**
     * returns the encoder to be used
     *
     * @return  stubStringEncoder
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * returns the encoder mode to be used
     *
     * @return  int
     */
    public function getEncoderMode()
    {
        return $this->encoderMode;
    }

    /**
     * execute the filter
     *
     * @param   string  $value
     * @return  string
     * @throws  stubFilterException
     */
    protected function doExecute($value)
    {
        return $this->encoder->apply($value, $this->encoderMode);
    }
}
?>