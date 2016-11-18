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
            $csvRow['Issue Id'] = (string) $issue->attributes()->id;

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
        $string = $newValue = $this->cleanValue($value);

        if ($header == 'State') {
            $this->convertState($csvRow, $value, $newValue);
        }

        if (in_array($header, ['Resolved', 'Created', 'Updated'])) {
            $newValue = $this->convertDate($value);
        }

        //Add to the row
        $csvRow[$header] = $newValue;
    }

    /**
     * Change Some special characters
     * @param  string $value Value
     * @return string        New Value
     */
    private function cleanValue($value)
    {
        $value = str_replace("„", '"', $value);
        $value = str_replace("“", '"', $value);
        return $value;
    }

    /**
     * Modify state
     * @param  array &$csvRow   Current CSV row
     * @param  string $value    Value
     * @param  string &$newValue New Value
     */
    private function convertState(&$csvRow, $value, &$newValue)
    {
        switch ($value) {
            case "Transported":
                $newValue = 'Closed';
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

    /**
     * Convert from Youtrack Timestamp to a readable time
     * @param  string $value Youtrack timestamp, which is milliseconds since 01.01.1970
     * @return string        Date in requested format (dd.MM.yyyy HH:mm:ss)
     */
    private function convertDate($value)
    {
        $seconds = $value / 1000;
        return date('d.m.Y H:i:s', $seconds);
    }
}
