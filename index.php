<?php
//Auto load classes
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

// Read from File
$source = new FileReader("sample2.xml");

// Parse to XML
$xmlParser = new XmlParser($source);
$xml = $xmlParser->parse();

// Convert to CSV and write to file
$csvFromXml = new CsvFromXml($xml);
$csvFromXml->write("filename.csv");
