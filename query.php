<?php
include_once('globals.php');
include_once('class/class.HTTPAnswer.php');
include_once('class/class.MySQL.php');
	
//conetc to mysql database,user,password,host
$oMySQL = new MySQL($dbName, $dbUser, $dbPasswd, $dbHost, $dbPort);
$HTTPAnswer = new HTTPAnswer();

//Test if the parameter is filled
if(isset($_GET["par"])){
    $Par = filter_var($_GET["par"], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
}elseif(isset($_POST["par"])){
    $Par = filter_var($_POST["par"], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
}else{
    $HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"-1|No data received.",true);
}
	
//
$SplittedData = array();
$SplittedData = explode("|", $Par);
//kontrola poctu pipe pokud nesedi vraci error a uklada log do tabulky error_log
if (sizeof($SplittedData)<=28 && $Par!='dbtest'){
    $Res = $oMySQL->ExecuteSQL("INSERT INTO error_log (LogString,ErrorType) VALUES ('$Par','Imput data mismatch')");
    $Flog = CreateTextLog($Par, $LogPath, 'ERROR_LOG');
    $HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"-1|Input data mismatch.",true);
}

//kontrola proti vlozeni nechtenych znaku a orezani netisknutelnych znaku
foreach ($SplittedData as $key => $value){
    $value = mysql_escape_string(trim($value));
    //zamena carky v desetine ciselne odnote za tecku
    $value = str_replace(',', '.', $value);
    
    //if(strlen($value)==0)$HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"-1|Input data mismatch.",true);
    $SplittedData[$key]=$value;
}
	
$MachineStatus = '';
$MachineStatus = array_shift($SplittedData);
	
switch($MachineStatus) {
   case 'phptest': $HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"1|OK",true);
   break;
	
   case 'dbtest':
    if(@$oMySQL->ExecuteSQL('SELECT * FROM `data_log` LIMIT 1')){
	$HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"2|OK",true);
    }else{
	$HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"0|DB_NO_OK",true);
    }
   break;
   case 'data':
       //Priprava dat pro vlozeni do db
       $LogArr = array(
	   'MeasureTime' => $SplittedData[0],
	   'InnerCode' => $SplittedData[1],
	   'GlobalResult' => $SplittedData[2],
	   'BulbCurentResult' => $SplittedData[3],
	   'BulbCurentMeasured' => $SplittedData[4],
	   'BulbCurentMeasuredMax' => $SplittedData[5],
	   'BulbCurentMeasuredMin' => $SplittedData[6],
	   'BulbVoltageResult' => $SplittedData[7],
	   'BulbVoltageMeasured' => $SplittedData[8],
	   'TestLengthMeasured' => $SplittedData[9],
	   'TestLengthMeasuredMin' => $SplittedData[10],
	   'TestLengthMeasuredMax' => $SplittedData[11],
	   'TestBlinkShine01' => $SplittedData[12],
	   'TestBlinkDark01' => $SplittedData[13],
	   'TestBlinkShine02' => $SplittedData[14],
	   'TestBlinkDark02' => $SplittedData[15],
	   'TestBlinkShine03' => $SplittedData[16],
	   'LeaksResults' => $SplittedData[17],
	   'LeaksMeasured' => $SplittedData[18],
	   'LeaksAllowed' => $SplittedData[19],
	   'PressureDest' => $SplittedData[20],
	   'PressureMin' => $SplittedData[21],
	   'PressureMax' => $SplittedData[22],
	   'PressureActual' => $SplittedData[23],
	   'PressureTime' => $SplittedData[24],
	   'PressureMeasuredTime' => $SplittedData[25],
	   'PressureAfterDelaysMin' => $SplittedData[26],
	   'PressureDelays' => $SplittedData[27],
	);
    //Zapis logu do DB 
    $result = $oMySQL->Insert($LogArr, 'data_log');
    if(!$result)
	$HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"0|INSERTNOK",true);
	   
    //Zapis logu do Souboru
    $Flog = CreateTextLog($Par, $LogPath, 'OK_LOG');
	
    if(!$Flog)
	$HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"0|WRITENOK",true);
		
    //Pokud zapis do souboru a do db je OK pak vraci OK
    $HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"3|INSERTOK",true);
	  
   break;
	       
   default:
     $HTTPAnswer->HTTPAnswer(HTTP_ANSWER_STATUS_200,"-1|Wrong service name.",true);
   break;
}

function CreateTextLog($LogString,$LogPath,$LogName){
    $Date = date('Ymd');
    $fh = fopen($LogPath.'\\'.$Date.'_'.$LogName.'.txt', "a");
    if($fh===FALSE)return FALSE;
    fputs ($fh, $LogString."\n");
    fclose ($fh);
    return TRUE;
}
	