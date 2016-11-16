<?php

class CsvFromXml
{
    private $xml;
    private $csv;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->xml = $xml;
        $this->convertData($this->xml);
    }

    private function convertData(SimpleXMLElement $xml)
    {
        $children = $xml->children();
        foreach ($children as $child) {
            MyHelper::fPrint($child->attributes());exit;
        }
    }

    public function write(String $filename)
    {
        echo $filename;exit;
    }
}
