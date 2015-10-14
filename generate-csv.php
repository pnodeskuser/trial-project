<?php 
// Include the Dropbox libraries
require_once "dropbox-sdk/autoload.php";
use \Dropbox as dbx;

// Basic configuration
// Setting the app key, secure key and api token
require_once "config/config.php";
$appInfo = dbx\AppInfo::loadFromJsonFile("config/config.json");
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
$dbxClient = new dbx\Client(apiToken, "PHP-Example/1.0");

// Reading data-a.csv file
$dataACSV = "data-a.csv";
$dataACSVOpened = fopen($dataACSV, "w+b");
$dbxClient->getFile("/".$dataACSV, $dataACSVOpened);
fclose($dataACSVOpened);

// Reading data-b.csv file
$dataBCSV = "data-b.csv";
$dataBCSVOpened = fopen($dataBCSV, "w+b");
$dbxClient->getFile("/".$dataBCSV, $dataBCSVOpened);
fclose($dataBCSVOpened);

// Compairing the csv contents on behalf of relation between the 2nd column of data-a.csv file and 5th column of data-b.csv file. 
// Variable: $fileCompareFrom, Consist the data-a.csv file content to compare from data-b.csv content 
// Variable: $fileCompareAgainst, Consist the data-b.csv file content to compare against data-a.csv content
$fileCompareFrom = fopen($dataACSV,"r");
$key = 0; 
$finalCSVArray = array();
while(! feof($fileCompareFrom)) {
	$array1 = fgetcsv($fileCompareFrom);
	$fileCompareAgainst = fopen($dataBCSV,"r");
	while(! feof($fileCompareAgainst)) {
		$array2 = fgetcsv($fileCompareAgainst);
		if($array1[1]!="" && $array2[4]!="" && $array1[1] == $array2[4]){
			$finalCSVArray[$key] = array($array1[0],$array1[2],$array1[4],$array1[5],$array2[0],$array2[2],$array2[3]);
			$key++;
			break;
		}
	}
}

// Creating Output CSV File (i.e. data-c.csv)
$finalCSV = "data-c.csv";
if(file_exists($finalCSV))unlink($finalCSV); 	// Deleting already existed data-c.csv
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$finalCSV);
$finalCSVOpened = fopen('php://output',"w");
chmod($finalCSV, 0777); 	// Setting read write permission to the file
foreach ($finalCSVArray as $line) {
	fputcsv($finalCSVOpened,$line);
}
fclose($finalCSVOpened);