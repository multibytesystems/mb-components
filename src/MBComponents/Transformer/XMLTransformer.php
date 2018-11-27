<?php

namespace MBComponents\Transformer;

class XMLTransformer
{
    protected $host;
    protected $xmlsrc = null; // This hold an xml-source (a file or an iXMLProvider-object or a DomDocument)
    protected $xslsrc = null; // This hold an xsl-source (a file)
    protected $xmldoc; // This holds the xsl-document
    protected $xsldoc; // This holds the xsl-document
    protected $proc = null; // This holds the xslt-tarnsformer
    protected $contentTypeDeclaration = false;
    protected $doc;

    protected static $counter = 0;
    protected $id;
    protected $parameters = null;
    private static $instances = array();

    /* ******* ******* ******* ******* ******* ******* ******* ******* */
    /* Private Fuctions START */
    /* ******* ******* ******* ******* ******* ******* ******* ******* */

    protected function __construct($xmlsrc)
    {
        $this->xmlsrc = $xmlsrc;
        $this->host = $_SERVER['HTTP_HOST'];
        $this->id = self::$counter;
        $this->extractXML();
    }

    private function __clone()
    {
    }

    public function __tostring()
    {
        return 'XMLTransformer'.$this->id;
    }

    // This retrieves the xml-document depending on the provided input and assigns it to $xmldoc
    protected function extractXML()
    {
        //set_error_handler('HandleXmlError');
        if (is_string($this->xmlsrc))
        {
            $this->xmldoc = new DOMDocument;
            $xmlfile = (substr_count($this->xmlsrc,'http://') == 0) ? 'http://'.$this->host.$this->xmlsrc : $this->xmlsrc;
            return $this->xmldoc->load($xmlfile);
        }
        else if ($this->xmlsrc instanceof \DomDocument)
        {
            $this->xmldoc = $this->xmlsrc;
            return $this->xmldoc;
        }
        restore_error_handler();
    }

    // This retrieves the xsl-document and returns it to $xsldoc
    protected function extractXSL($xslsrc)
    {
        //set_error_handler('HandleXmlError');
        $this->xslsrc = $xslsrc;
        $this->xsldoc = new \DOMDocument;
        //$this->xslfile = (substr_count($this->xslsrc,'http://') == 0) ? 'http://'.$this->host.$this->xslsrc : $this->xslsrc;
        $this->xslfile = $this->xslsrc;
        $this->xsldoc->load($this->xslfile);
        restore_error_handler();
    }

    protected function configureXSLTProcessor()
    {
        if ($this->proc === null) $this->proc = new \XSLTProcessor;
        $this->proc->importStyleSheet($this->xsldoc);
        $this->proc->registerPHPFunctions();
    }

    protected function setProcessingParameters($params)
    {
        $this->parameters = &$params;
        if ($this->parameters != null)
        {
            foreach ($this->parameters as $key => $value)
            {
                $this->proc->setParameter('',$key, $value);
            }
        }
    }

    protected function transform($xslsrc,$params=null)
    {
        if ($this->xslsrc === null || $xslsrc != $this->xslsrc)
        {
            $this->extractXSL($xslsrc);
            $this->configureXSLTProcessor();
        }
        if ($this->parameters === null || ($params != null && is_array($params))) $this->setProcessingParameters($params);
        $this->doc = $this->proc->transformToDoc($this->xmldoc);
        $this->doc->formatOutput = true;
    }

    /* ******* ******* ******* ******* ******* ******* ******* ******* */
    /* Private Fuctions END */
    /* ******* ******* ******* ******* ******* ******* ******* ******* */

    /* ******* ******* ******* ******* ******* ******* ******* ******* */
    /* Public Fuctions START */
    /* ******* ******* ******* ******* ******* ******* ******* ******* */

    public static function getInstance($xmlsrc)
    {
        self::$counter ++;
        if (is_string($xmlsrc))
        {
            if (!isset(self::$instances[$xmlsrc]))
            {
                self::$instances[$xmlsrc] = new XMLTransformer($xmlsrc);
            }
            return self::$instances[$xmlsrc];
        }
        else if ($xmlsrc instanceof \DomDocument)
        {
            return new XMLTransformer($xmlsrc);
        }
    }

    public function includeContentTypeDeclaration($contentTypeDeclaration=true)
    {
        $this->contentTypeDeclaration = $contentTypeDeclaration;
    }

    public function transformXML($xslsrc,$params=null)
    {
        $this->transform($xslsrc,$params);
        return $this->doc->saveXML();
    }

    public function transformToXML($xslsrc,$params=null)
    {
        $this->transform($xslsrc,$params);
        if ($this->contentTypeDeclaration === true) Header ("Content-type: text/xml");
        return $this->doc->saveXML();
    }

    public function transformToHtml($xslsrc,$params=null)
    {
        $this->transform($xslsrc,$params);
        return $this->doc->saveHTML();
    }

    public function getSourceXML()
    {
        Header ("Content-type: text/xml");
        if (is_string($this->xmlsrc)|| $this->xmlsrc instanceof DomDocument)
        {
            return $this->xmldoc->saveXML();
        }
        elseif ($this->xmlsrc instanceof iXMLProvider)
        {
            return $this->xmlsrc->getXmlAsString();;
        }
    }

    public function getDomDocument()
    {
        return $this->doc;
    }

    public function getXmlAsString()
    {
        if ($this->contentTypeDeclaration === true) Header ("Content-type: text/xml");
        return $this->doc->saveXML();
    }

    public function getXmlAsHtmlString()
    {
        $this->doc->encoding = "utf-8";
        return "<pre style=\"border:4px solid #888888; background-color:#eeeeee; padding:12px; overflow:auto;\">".htmlentities($this->doc->saveXML())."</pre>";
    }

    /* ******* ******* ******* ******* ******* ******* ******* ******* */
    /* Public Fuctions END */
    /* ******* ******* ******* ******* ******* ******* ******* ******* */
}
?>