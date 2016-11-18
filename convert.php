<?php
require_once('autoload/autoload.php');

//Console arguments
$inputFileName = $argv[1];
$outputFileName = $argv[2];

// Read from File
$source = MyHelper::readFileAsString($inputFileName);

// Parse to XML
$xml = MyHelper::parseXml($source);

//define fields [key and value] to read from XML and convert to CSV columns
$fields = [
    'projectShortName' => 'Project',
    'summary' => 'Summary',
    'reporterFullName' => 'Reporter',
    'created' => 'Created',
    'updated' => 'Updated',
    'resolved' => 'Resolved',
    'Priority' => 'Priority',
    'Type' => 'Type',
    'State' => 'State',
    'Assignee' => 'Assignee',
    'Fix versions' => 'Fix Version',
    'AOK Hinweis Nr.' => 'AOK Hinweis Nr.',
    'Branch' => 'Branch',
    'Relevant to' => 'Relevant to',
    'HEK Hinweis Nr.' => 'HEK Hinweis Nr.',
    'Documentation' => 'Documentation',
    'Korrektur ab Release' => 'Korrektur ab Release',
    'description' => 'Description',
];

// Convert to CSV and write to file
// $csvFromXml = new CsvFromXml($xml, $fields, $delimiter = ",", $enclosure = "^");
$csvFromXml = new CsvFromXmlOgs($xml, $fields, $delimiter = ",", $enclosure = '"');
$csvFromXml->write($outputFileName);
