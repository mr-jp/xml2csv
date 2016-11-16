<?php

class CsvFromXml
{
    private $xml;
    private $fields;
    private $csvArray;
    private $delimiter;
    private $enclosure;
    private $escape_char;

    public function __construct(SimpleXMLElement $xml, array $fields = [], string $delimiter = ",", string $enclosure = '"', string $escape_char = '\\')
    {
        $this->xml = $xml;
        $this->fields = $fields;
        $this->convertData($this->xml);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape_char = $escape_char;
    }

    private function convertData(SimpleXMLElement $xml)
    {
        // MyHelper::fPrint($xml);exit;
        $issues = $xml->children();
        $csvArray = [];

        foreach ($issues as $issue) {
            $csvRow = [];
            $csvRow['Issue Id'] = (string) $issue->attributes()->id;

            foreach ($issue->field as $fieldObject) {
                $fieldName = (string) $fieldObject->attributes()->name;
                if (array_key_exists($fieldName, $this->fields)) {
                    $csvRow[$this->fields[$fieldName]] = (string) $fieldObject->value;
                }
            }

            $csvArray[] = $csvRow;
        }
        $this->csvArray = $csvArray;
    }

    public function write(String $filename)
    {
        $resource = fopen($filename, 'w');

        //output header row
        $firstItem = $this->csvArray[0];
        fputcsv($resource, array_keys($firstItem), $this->delimiter, $this->enclosure, $this->escape_char);

        //output data
        foreach ($this->csvArray as $row) {
            fputcsv($resource, $row, $this->delimiter, $this->enclosure, $this->escape_char);
        }

        //Close resource
        fclose($resource);
    }
}
