<?php

class CsvFromXml
{
    protected $xml;
    protected $fields;
    protected $csvArray;
    protected $delimiter;
    protected $enclosure;
    protected $escape_char;

    public function __construct(SimpleXMLElement $xml, array $fields = [], string $delimiter = ",", string $enclosure = '"', string $escape_char = '\\')
    {
        $this->xml = $xml;
        $this->fields = $fields;
        $this->convertData($this->xml);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape_char = $escape_char;
    }

    /**
     * Convert data for XML to CSV
     * @param  SimpleXMLElement $xml XML object
     * @return void
     */
    protected function convertData(SimpleXMLElement $xml)
    {
        $issues = $xml->children();
        $csvArray = [];

        foreach ($issues as $issue) {
            $issueRow = [];
            $csvRow = [];

            //Gather all items from the XML into issueRow
            foreach ($issue->field as $fieldObject) {
                $fieldName = (string) $fieldObject->attributes()->name;
                $value = (string) $fieldObject->value;
                $issueRow[$fieldName] = $value;
            }

            //Loop through all fields, we need to add them to CSV even though there is no row in XML
            foreach ($this->fields as $fieldName => $header) {
                if (array_key_exists($fieldName, $issueRow)) {
                    $value = $issueRow[$fieldName];
                } else {
                    $value = "";
                }

                //Add to csvRow
                $this->processRow($csvRow, $header, $value);
            }

            $csvArray[] = $csvRow;
        }
        $this->csvArray = $csvArray;
    }

    /**
     * Some items have special rules (should be overridden in children classes)
     * @param  array &$csvRow The current row
     * @param  string $header Header name
     * @param  string $value  Value of the item
     */
    protected function processRow(&$csvRow, $header, $value)
    {
        $string = $newValue = $value;
        $csvRow[$header] = $newValue;
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
