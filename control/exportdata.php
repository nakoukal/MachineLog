<?php
session_start();
include_once('../globals.php');
include_once('../class/class.MySQL.php');
include_once('../class/class.DataTable.php');

$oMySQL = new MySQL($dbName, $dbUser, $dbPasswd, $dbHost, $dbPort);
$dataTable = new DataTable($oMySQL);
$dataTable->ExportToCSV();

