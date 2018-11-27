<?php

namespace MBComponents\Serializer\Adapter;

use Zend\Serializer\Adapter\AbstractAdapter;

class Xml extends AbstractAdapter
{
    /**
     * @var \DomDocument
     */
    protected $doc = null;

    /**
     * @var \DomElement
     */
    protected $root = null;

    /**
     * @var XmlOptions
     */
    protected $options = null;

    /**
     * Set options
     *
     * @param  array|\Traversable|XmlOptions $options
     * @return Xml
     */
    public function setOptions($options)
    {
        if (!$options instanceof XmlOptions) {
            $options = new XmlOptions($options);
        }

        $this->options = $options;
        return $this;
    }

    /**
     * Get options
     *
     * @return XmlOptions
     */
    public function getOptions()
    {
        if ($this->options === null) {
            $this->options = new XmlOptions();
        }

        return $this->options;
    }

    /**
     * Serialize PHP value to Xml
     *
     * @param  mixed $value
     * @return string
     */
    public function serialize($value)
    {
        if (is_array($value)) {
            $df = $this->createDocument($value);
            if ($df->hasChildNodes()) $this->doc->appendChild($df->cloneNode(true));
            return $this->doc->saveXml();
        }
        else {

        }
    }

    /**
     * Serialize PHP value to Xml
     *
     * @param  mixed $value
     * @return \DomDocument
     */
    public function getDomDocument($value)
    {
        if (is_array($value)) {
            $df = $this->createDocument($value);
            if ($df->hasChildNodes()) $this->doc->appendChild($df->cloneNode(true));
            return $this->doc;
        }
        else {

        }
    }

    /**
     * Deserialize JSON to PHP value
     *
     * @param  string $json
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function unserialize($json)
    {
        return '';
    }

    protected function createDocument($res)
    {
        $keys = $this->getOptions()->getKeys();

        $this->doc = new \DomDocument();
        $this->doc->formatOutput = true;
        $this->doc->encoding = $this->getOptions()->getEncoding();

        $df = $this->doc->createDocumentFragment();
        $root = $this->doc->createElement($this->getOptions()->getRootElementName());
        $this->root = $root;
        if ($this->getOptions()->getFieldsAsElements() === true && $this->getOptions()->getFieldsAsAttributes() === false &&  count($keys) == 0 &&  count($this->getOptions()->getFieldsWithCDATA()) == 0)
        {
            $retdf = $this->simpleXMLTagsGenerator($res);
        }
        else if ($this->getOptions()->getFieldsAsElements() === false && $this->getOptions()->getFieldsAsAttributes() === true &&  count($keys) == 0 &&  count($this->getOptions()->getFieldsWithCDATA()) == 0)
        {
            $retdf = $this->simpleXMLAttributesGenerator($res);
        }
        else
        {
            $retdf = $this->complexXMLGenerator($res);
        }
        if ($retdf->hasChildNodes()) $df->appendChild($retdf);

        if ($df->hasChildNodes()) $root->appendChild($df);
        $df->appendChild($root);

        return $df;
    }

    protected function simpleXMLTagsGenerator($res)
    {
        $df = $this->doc->createDocumentFragment();
        foreach($res as $k => $record)
        {
            if (is_array($record)) {
                $rec = $this->doc->createElement($this->getOptions()->getRecordName());
                foreach($record as $key => $val)
                {
                    $txt = $this->doc->createTextNode($val);
                    $field = $this->doc->createElement($key);
                    $field->appendChild($txt);
                    $rec->appendChild($field);
                }
                $df->appendChild($rec);
            }
            else {
                $txt = $this->doc->createTextNode($record);
                $field = $this->doc->createElement($k);
                $field->appendChild($txt);
                $df->appendChild($field);
            }
        }
        return $df;
    }

    protected function simpleXMLAttributesGenerator($res)
    {
        $df = $this->doc->createDocumentFragment();
        foreach($res as $k => $record)
        {
            if (is_array($record)) {
                $rec = $this->doc->createElement($this->getOptions()->getRecordName());
                foreach($record as $key => $val)
                {
                    $txt = $this->doc->createTextNode($val);
                    $field = $this->doc->createAttribute($key);
                    $field->appendChild($txt);
                    $rec->appendChild($field);
                }
                $df->appendChild($rec);
            }
            else {
                $txt = $this->doc->createTextNode($record);
                $field = $this->doc->createAttribute($k);
                $field->appendChild($txt);
                $this->root->appendChild($field);
            }
        }
        return $df;
    }

    protected function complexXMLGenerator($res)
    {
        $keys = $this->getOptions()->getKeys();
        $df = $this->doc->createDocumentFragment();
        foreach($res as $k => $record)
        {
            if (is_array($record)) {
                $rec = $this->doc->createElement($this->getOptions()->getRecordName());
                foreach($record as $key => $val)
                {
                    $xmlFieldName = (isset($keys[$key])) ? $keys[$key] : $key;
                    if ($this->getOptions()->getFieldsAsAttributes() === true || (is_array($this->getOptions()->getFieldsAsAttributes()) && in_array($key,$this->getOptions()->getFieldsAsAttributes())))
                    {
                        $txt = $this->doc->createTextNode($val);
                        $field = $this->doc->createAttribute($xmlFieldName);
                        $field->appendChild($txt);
                        $rec->appendChild($field);
                    }
                    if ($this->getOptions()->getFieldsAsElements() === true || (is_array($this->getOptions()->getFieldsAsElements()) && in_array($key,$this->getOptions()->getFieldsAsElements())))
                    {
                        $txt = (in_array($key,$this->getOptions()->getFieldsWithCDATA())) ? $this->doc->createCDATASection($val) : $this->doc->createTextNode($val);
                        $field = $this->doc->createElement($xmlFieldName);
                        $field->appendChild($txt);
                        $rec->appendChild($field);
                    }
                }
                $df->appendChild($rec);
            }
            else {
                $xmlFieldName = (isset($keys[$k])) ? $keys[$k] : $k;
                if ($this->getOptions()->getFieldsAsAttributes() === true || (is_array($this->getOptions()->getFieldsAsAttributes()) && in_array($k,$this->getOptions()->getFieldsAsAttributes())))
                {
                    $txt = $this->doc->createTextNode($record);
                    $field = $this->doc->createAttribute($xmlFieldName);
                    $field->appendChild($txt);
                    $this->root->appendChild($field);
                }
                if ($this->getOptions()->getFieldsAsElements() === true || (is_array($this->getOptions()->getFieldsAsElements()) && in_array($k,$this->getOptions()->getFieldsAsElements())))
                {
                    $txt = (in_array($k,$this->getOptions()->getFieldsWithCDATA())) ? $this->doc->createCDATASection($record) : $this->doc->createTextNode($record);
                    $field = $this->doc->createElement($xmlFieldName);
                    $field->appendChild($txt);
                    $df->appendChild($field);
                }
            }
        }
        return $df;
    }
}
