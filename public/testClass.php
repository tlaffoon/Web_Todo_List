<?php

// $objectOne = new ProcessCSV('../data/week6.csv');
// $parsedData = $objectOne->parseCSV();

require('./filestore.php');

$txtObject = new Filestore('./testfile.txt');
$lines = $txtObject->readTXT($txtObject->filename);

var_dump($lines);

$csvObject = new Filestore('./testfile.csv');
$entries = $csvObject->readCSV($csvObject->filename);

var_dump($entries);

