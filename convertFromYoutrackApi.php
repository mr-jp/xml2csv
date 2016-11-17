<?php
//Auto load classes
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

// Read from Youtrack API
    $configFilename = "youtrack.config";
    $filter = "-feature";
    $project = 'OGS';
    $step = '100'; //how many issues to export at once from the API
    $exportFolder = '/export';

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

//Get issue count
$youtrackIssueCount = new YoutrackIssueCount($configFilename);
$issueCount = $youtrackIssueCount->getCount();

//Export with steps
for ($i = 0; $i < $issueCount; $i += $step) {
    $start = $i;
    $end = $i + $step - 1;
    $outputFilename = "ogs_export_{$start}_to_{$end}.csv";
    // echo "$start to $end \n";

    $after = $start;
    $max = $step;
    $source = new YoutrackIssuesReader($configFilename, $after, $max, $project, $filter);

    // Parse to XML
    $xmlParser = new XmlParser($source);
    $xml = $xmlParser->parse();

    // Convert to CSV and write to file
    $csvFromXml = new CsvFromXmlOgs($xml, $fields, $delimiter = ",", $enclosure = '"');
    $csvFromXml->write($outputFilename);
}
