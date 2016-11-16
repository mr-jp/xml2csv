<?php
//Auto load classes
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

// Read from File
$source = new FileReader("sample50.xml");

// Parse to XML
$xmlParser = new XmlParser($source);
$xml = $xmlParser->parse();

//define fields [key and value]
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
$csvFromXml = new CsvFromXml($xml, $fields, $delimiter = ",", $enclosure = "^");
$csvFromXml->write("filename.csv");
