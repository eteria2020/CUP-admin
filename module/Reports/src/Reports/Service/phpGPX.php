<?php

/**
 * GoogleKML is PHP class used for create Google KML file.
 *
 * @name phpGoogleKML
 *
 * @author Peter Misovic - Thailon
 * @copyright GPL licence
 *
 * @link http://internetgis.net/projects/geoclasses/phpGoogleKML
 *
 * @version 0.2
 *
 * HISTORY
 * ver 0.2 - first oficial version
 * + generate line placemarks added
 * + set of encoding added
 * + set of xmlns version added
 * ver 0.1 - initial version
 * + generate only point palcemarks
 **/
class phpGPX
{
    // PROPERTIES

    // xmnls
    private $creator = 'phpGPX';
    private $xmlns = 'http://www.topografix.com/GPX/1/1';
    private $xmlns_xsi = 'http://www.w3.org/2001/XMLSchema-instance';
    private $xmnls_xsi_schemaLocation = 'http://www.topografix.com/GPX/1/1';
    private $xmnls_xsd = 'http://www.topografix.com/GPX/1/1/gpx.xsd';

    // xml
    private $xml_encoding = 'UTF-8';
    private $xml_version = '1.0';

    private $metadata = '';

    // system
    private $outputDirectory = './';
    private $filename = 'phpGPX.gpx';
    private $resource;

    //var $KML_name = "KML name was not defined. Using default.";
    //var $KML_description = "KML description was not defined. Using default.";

    private $errorMessage = '';
    public $file_content = '';
    public $pointWpt = '';
    public $lineWpt = '';
    public $footer = '';
    public $header = '';
    private $xml_tag = '';
    public $gpx_tag = '';
    public $metadata_tag = '';

    public function __construct()
    {
    }

    // INTERNAL METHODS
    public function ValidateOutputDirecotry()
    {
        if (!file_exists($this->outputDirectory)) {
            die('Output directory does not exist! Please create valid directory.');
        }
        if (!is_dir($this->outputDirectory)) {
            die('Not an directory! Please enter valid directory.');
        }
        if (!is_writable($this->outputDirectory)) {
            die('Direcotry is not writable! Please set appertaining permissions.');
        }
    }

    public function GetXmlTag()
    {
        $this->xml_tag = '<?xml version="'.strip_tags(trim($this->xml_version)).'" encoding="'.strip_tags(trim($this->xml_encoding))."\"?>\n";

        return $this->xml_tag;
    }

    public function GetGpxTag()
    {
        $this->gpx_tag = '<gpx creator="'.strip_tags(trim($this->creator)).'" xmlns="'.strip_tags(trim($this->xmlns)).'" xmlns:xsi="'.strip_tags(trim($this->xmlns_xsi)).'" xsi:schemaLocation="'.strip_tags(trim($this->xmnls_xsi_schemaLocation)).' '.strip_tags(trim($this->xmnls_xsd))."\">\n";

        return $this->gpx_tag;
    }

    public function GetMetadataTag()
    {
        if (!empty($this->metadata)) {
            $this->metadata_tag = '<metadata>'.strip_tags(trim($this->metadata))."</metadata>\n";
        } else {
            $this->metadata_tag = "<metadata/>\n";
        }

        return $this->metadata_tag;
    }

    /**
     * This internal method returns KML file header based on user defined (or pre-defined) kml or kml parameters.
     *
     * @return string
     */
    public function CreateHeader()
    {
        $this->header .= $this->GetXmlTag();
        $this->header .= $this->GetGpxTag();
        $this->header .= $this->GetMetadataTag();

        return $this->header;
    }

    public function StartTrack($name)
    {
        $this->pointWpt .= "<trk><name>$name</name><trkseg>";
    }

    public function EndTrack()
    {
        $this->pointWpt .= '</trkseg></trk>';
    }

    public function CreateFooter()
    {
        $this->footer .= "</gpx>\n";

        return $this->footer;
    }

 //<time>2011-07-13T07:53:42+0000</time
    public function addTrackPoint($time, $lat, $lon)
    {
        $this->pointWpt .= '<trkpt lat="'.$lat.'" lon="'.$lon."\">\n";
        $this->pointWpt .= '<time>'.$time."</time>\n";
        $this->pointWpt .= "</trkpt>\n";
    }

    // EXTERNAL METHODS
    public function addPoint($name, $cmt, $sym, $type, $description, $latitude, $longitude)
    {
        $this->pointWpt .= '<wpt lat="'.$latitude.'" lon="'.$longitude."\">\n";
        $this->pointWpt .= '<name>'.$name."</name>\n";
        //$this->pointWpt .= "<cmt>".$cmt."</cmt>\n";
        //$this->pointWpt .= "<desc><![CDATA[".$description."]]></desc>\n";
        //$this->pointWpt .= "<sym>".$sym."</sym>\n";
        //$this->pointWpt .= "<type>".$type."</type>\n";
        $this->pointWpt .= "</wpt>\n";

        return $this->pointWpt;
    }

    public function addLine()
    {
    }

    /**
     * This external method creates the KML file.
     *
     * @todo add ValidateFile method.   
     */
    public function CreateGPXfile()
    {
        $this->ValidateOutputDirecotry();
        $this->resource = fopen($this->outputDirectory.$this->filename, 'w+');
        if ($this->resource) {
            $this->file_content .= $this->CreateHeader();
            $this->file_content .= $this->pointWpt;
            $this->file_content .= $this->lineWpt;
            $this->file_content .= $this->CreateFooter();
            if (!fputs($this->resource, $this->file_content, strlen($this->file_content))) {
                die('Error during KML file content writing.');
                unlink($this->outputDirectory.$this->filename);
            }
            fclose($this->resource);
        } else {
            die('File resource does not exists.');
        }
    }

    public function GetContent()
    {
        echo $this->CreateHeader();
        echo $this->pointWpt;
        echo $this->lineWpt;
        echo $this->CreateFooter();
    }

    public function DownloadGPXfile($download_type)
    {
        switch ($download_type) {
        case 'KML':
            header('Content-type: application/gpx');
            header('Content-Disposition: attachment; filename="'.$this->filename.'"');
            $this->GetContent();
        break;

        case 'TXT':
            header('Content-type: txt/txt');
            header('Content-Disposition: attachment; filename="'.$this->filename.'.txt"');
            $this->GetContent();
            /*echo $this->CreateHeader();
              echo $this->pointWpt;
              echo $this->lineWpt;
              echo $this->CreateFooter();*/
        break;
        }
    }

    public function GetContentToString()
    {
        return $this->CreateHeader().$this->pointWpt.$this->lineWpt.$this->CreateFooter();
    }

    /**
     * This external method displays created KML file in browser.
     */
    public function DisplayGPXfile()
    {
        echo highlight_string($this->CreateHeader().$this->pointWpt.$this->lineWpt.$this->CreateFooter(), 1);
    }
}
