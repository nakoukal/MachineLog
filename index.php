<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <meta http-equiv="cache-control" content="no-cache">
   <link rel="stylesheet"  href="css/bootstrap-combined.min.css">
   <link rel="stylesheet" type="text/css" media="screen"  href="css/bootstrap-datetimepicker.min.css">
   <link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>
   <link rel="stylesheet" type="text/css"  href="css/styl.css">
   
   <script src="jscripts/jquery.js" type="text/javascript"></script>
   <script src="jscripts/bootstrap.min.js" type="text/javascript"></script>
   <script src="jscripts/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
   <script src="jscripts/locales/bootstrap-datetimepicker.cs.js" type="text/javascript"></script>
   <script src="jscripts/bootstrap-multiselect.js" type="text/javascript"></script>
   <script src="jscripts/jquery.autocomplete.js" type="text/javascript"></script>
   <script type="text/javascript" src="jscripts/local.js"></script>
   <script type="text/javascript">
   $(document).ready(function(){
    $(function() {
	$('#datefrom_picker').datetimepicker({
	    language: 'cs',
	    pick12HourFormat: false
	});
    });
	
    $(function() {
	$('#dateto_picker').datetimepicker({
	language: 'cs',
	pick12HourFormat: false
	});
    });
    
    $('#button').click(function(){
	var string = $('#string').val();

    $.get('test.php', { input: string }, function(data) {  
     $('#feedback').text(data);
	});
    });

    var options, a;
    jQuery(function(){
    options = { serviceUrl:'control/autocompleter.php' };
    a = $('#InnerCode').autocomplete(options);
    });
});
    </script>
  
  <title>Data Logs</title>

  </head>
  <body>
<?php
session_start();
include_once('globals.php');
include_once('class/class.MySQL.php');
include_once('class/class.DataTable.php');

$oMySQL = new MySQL($dbName, $dbUser, $dbPasswd, $dbHost, $dbPort);
$dataTable = new DataTable($oMySQL);
$dataTable->showtable();

?>

  </body>
</html>