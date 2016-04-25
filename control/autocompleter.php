<?php
include_once('../globals.php');
include_once('../class/class.MySQL.php');
include_once('../class/class.DataTable.php');
$oMySQL = new MySQL($dbName, $dbUser, $dbPasswd, $dbHost, $dbPort);

$query = '';
if(isset($_GET['query']))
	    $query = filter_var($_GET['query'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);

$dataTable = new DataTable($oMySQL);
echo $dataTable->GetInnerCode($query);
