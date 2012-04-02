<?php
/**
 * Class to transfer image data into an xml document.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 * @version     $Id: stubXslImageDimensionsCallback.php 2867 2011-01-10 17:02:33Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::callback::stubXslCallbackException',
                      'net::stubbles::xml::xsl::callback::stubXslAbstractCallback'
);
/**
 * Class to transfer image data into an xml document.
 *
 * Lookup for image is done with the following steps:
 * 1. Find image in document root of current project. If not exists, try next.
 * 2. Find image in document root of common project. If not exists throw exception.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 */
class stubXslImageDimensionsCallback extends stubXslAbstractCallback
{
    /**
     * a list of file types where the key corresponds to the IMAGETYPE constants of PHP
     *
     * @var  array<int,string>
     */
    protected $types              = array('unknown', 'GIF', 'JPG', 'PNG', 'SWF', 'PSD', 'BMP',
                                          'TIFF(intel byte order)', 'TIFF(motorola byte order)',
                                          'JPC', 'JP2', 'JPX', 'JB2', 'SWC', 'IFF', 'WBMP', 'XBM'
                                    );
    /**
     * path to images in project docroot
     *
     * @var  string
     */
    protected $projectDocrootPath = null;
    /**
     * path to images in common docroot
     *
     * @var  string
     */
    protected $commonDocrootPath  = null;

    /**
     * constructor
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter     xml stream writer to create the document with
     * @param  string               $projectDocrootPath  path to docroot of current project
     * @param  string               $commonDocrootPath   path to docroot of common project
     * @since  1.5.0
     * @Inject
     * @Named{projectDocrootPath}('net.stubbles.docroot.path')
     * @Named{commonDocrootPath}('net.stubbles.docroot.path.common')
     */
    public function __construct(stubXMLStreamWriter $xmlStreamWriter, $projectDocrootPath, $commonDocrootPath)
    {
        parent::__construct($xmlStreamWriter);
        $this->projectDocrootPath = $projectDocrootPath;
        $this->commonDocrootPath  = $commonDocrootPath;
    }

    /**
     * takes a dom attribute and return the image informations for the first one
     *
     * @param   array<DOMAttr>|string  $imageFile
     * @return  DOMDocument
     * @throws  stubXslCallbackException
     * @XslMethod
     */
    public function getImageDimensions($imageSrc)
    {
        $imageFileName = $this->findImage($this->parseValue($imageSrc));
        $image         = @getimagesize($imageFileName);
        if (false === $image) {
            throw new stubXslCallbackException('Image ' . $imageFileName . ' seems not to be an image, can not retrieve dimension data.');
        }

        $this->xmlStreamWriter->writeStartElement('image');
        $this->xmlStreamWriter->writeElement('width', array(), $image[0]);
        $this->xmlStreamWriter->writeElement('height', array(), $image[1]);
        $this->xmlStreamWriter->writeElement('type', array(), $this->getType($image[2]));
        $this->xmlStreamWriter->writeElement('mime', array(), $image['mime']);
        $this->xmlStreamWriter->writeEndElement();
        $doc = $this->xmlStreamWriter->asDom();
        $this->xmlStreamWriter->clear();
        return $doc;
    }

    /**
     * finds image and returns complete path to image
     *
     * @param   string  $imageTag
     * @return  string
     * @throws  stubXslCallbackException
     */
    protected function findImage($imageSrc)
    {
        if (file_exists($this->projectDocrootPath . '/' . $imageSrc) === false) {
            if (file_exists($this->commonDocrootPath . '/' . $imageSrc) === false) {
                throw new stubXslCallbackException('Image ' . $imageSrc . ' does not exist.');
            }

            return $this->commonDocrootPath . '/' . $imageSrc;
        }

        return $this->projectDocrootPath . '/' . $imageSrc;
    }

    /**
     * returns the image type as string
     *
     * @param   int     $type
     * @return  string
     */
    protected function getType($type)
    {
        if (isset($this->types[$type]) == true) {
            return $this->types[$type];
        }

        return $this->types[0];
    }
}
?>