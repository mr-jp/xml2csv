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
            $issueRow = [];
            $csvRow = [];

            //Special case for Issue Id
            $issueRow['Issue Id'] = (string) $issue->attributes()->id;

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
     * Some items have special rules
     * @param  array &$csvRow The current row
     * @param  string $header Header name
     * @param  string $value  Value of the item
     */
    protected function processRow(&$csvRow, $header, $value)
    {
        $string = $newValue = $value;

        if ($header == 'State') {
            switch ($value) {
                case "Transported":
                    $newValue = 'Closed';
                    $resolution = 'Behoben';
                    $csvRow["Resolution"] = 'Behoben';
                    break;

                case "Obsolete":
                    $newValue = 'Closed';
                    $csvRow["Resolution"] = 'Wird nicht behoben';
                    break;

                case "Duplicate":
                    $newValue = 'Closed';
                    $csvRow["Resolution"] = 'Duplikat';
                    break;

                default:
                    $csvRow["Resolution"] = 'nicht erledigt';
                    break;
            }
        }

        //Add to the row
        $csvRow[$header] = $newValue;
    }
}
