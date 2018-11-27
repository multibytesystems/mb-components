<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace MBComponents\Serializer\Adapter;

use Zend\Serializer\Adapter\AdapterOptions;
use Zend\Serializer\Exception;

class XmlOptions extends AdapterOptions
{
    protected $encoding = "UTF-8"; //iso-8859-1
    protected $fieldsAsAttributes = false;
    protected $fieldsAsElements = true;
    protected $fieldsWithCDATA = array();
    protected $keys = array();
    protected $rootElementName = "document";
    protected $recordName = "record";


    public function setEncoding($encoding){
        $this->encoding=$encoding;
    }
    public function getEncoding(){
        return $this->encoding;
    }

    public function setFieldsAsAttributes($fieldsAsAttributes){
        $this->fieldsAsAttributes=$fieldsAsAttributes;
    }
    public function getFieldsAsAttributes(){
        return $this->fieldsAsAttributes;
    }

    public function setFieldsAsElements($fieldsAsElements){
        $this->fieldsAsElements=$fieldsAsElements;
    }
    public function getFieldsAsElements(){
        return $this->fieldsAsElements;
    }

    public function setFieldsWithCDATA($fieldsWithCDATA){
        $this->fieldsWithCDATA=$fieldsWithCDATA;
    }
    public function getFieldsWithCDATA(){
        return $this->fieldsWithCDATA;
    }

    public function setKeys($keys){
        $this->keys=$keys;
    }
    public function getKeys(){
        return $this->keys;
    }

    public function setRootElementName($rootElementName){
        $this->rootElementName=$rootElementName;
    }
    public function getRootElementName(){
        return $this->rootElementName;
    }

    public function setRecordName($recordName){
        $this->recordName=$recordName;
    }
    public function getRecordName(){
        return $this->recordName;
    }

}
