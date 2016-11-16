<?php

class CsvFromXmlOgs extends CsvFromXml
{
    /**
     * Convert data for Youtrack API from XML to CSV
     * @param  SimpleXMLElement $xml XML object
     * @return void
     */
    protected function convertData(SimpleXMLElement $xml)
    {
        $issues = $xml->children();
        $csvArray = [];

        foreach ($issues as $issue) {
            $csvRow = [];

            //Special case for Issue Id
            $csvRow['Issue Id'] = (string) $issue->attributes()->id;

            foreach ($issue->field as $fieldObject) {
                $fieldName = (string) $fieldObject->attributes()->name;
                if (array_key_exists($fieldName, $this->fields)) {
                    $header = $this->fields[$fieldName];
                    $value = (string) $fieldObject->value;
                    $this->processRow($csvRow, $header, $value);
                }
            }

            $csvArray[] = $csvRow;
        }
        $this->csvArray = $csvArray;
    }

    /**
     * Some items have special rules
     * @param  array &$csvRow The current row
     * @param  string $header Header name
     * @param  string $value  Value of the item
     */
    protected function processRow(&$csvRow, $header, $value)
    {
        $string = $newValue = $value;
        $jiraResolution = 'nicht erledigt';

        // if ($header == 'State') {
        //     switch ($value) {
        //         case "Transported":
        //         case "Obsolete":
        //         case "Duplicate":
        //     }
        // }

        //Add to the row
        $csvRow[$header] = $newValue;
    }
}
