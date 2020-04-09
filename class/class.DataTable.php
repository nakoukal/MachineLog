
<?php
//
// +------------------------------------------------------------------------+
// | DATA LOGS 
// +------------------------------------------------------------------------+
// | Copyright (C) 2014
// | Nakoukal Radek
// +------------------------------------------------------------------------+
//

  
/**
  * Class to processing logs from database server
  *
  * @package DataTable
  *
  * @author Nakoukal Radek
*/
class DataTable{
    private $_db;
    private $_pagePerSiteNumbers = array();
    private $_limit;
    private $_pagePerSite = 50;//Nastavení radku na stranku
    private $_filterItems = array();
    private $_selectedFilterItems = array('MeasureTime','InnerCode');//Nastaveni vychozich filtru
    private $_pageStart = 0;
    private $_dateFrom;
    private $_dateTo;
    private $_where = array();
    private $_innerCode;
    private $_globalResult;
    private $_bulbCurentResult;
    private $_bulbCurentMeasured;
    private $_bulbCurentMeasuredMax;
    private $_bulbCurentMeasuredMin;
    private $_bulbVoltageResult;
    private $_bulbVoltageMeasured;
    private $_testLengthMeasured;
    private $_testLengthMeasuredMin;
    private $_testLengthMeasuredMax;
    private $_testBlinkShine01;
    private $_testBlinkShine02;
    private $_testBlinkDark02;
    private $_testBlinkShine03;
    private $_leaksResults;
    private $_leaksMeasured;
    private $_leaksAllowed;
    private $_pressureDest;
    private $_pressureMin;
    private $_pressureMax;
    private $_pressureActual;
    private $_PressureTime;
    private $_pressureMeasuredTime;
    private $_pressureAfterDelaysMin;
    private $_pressureDelays;
    private $_countRecords;
    
    private $_sort = 'MeasureTime DESC'; //Vychozi nastaveni trideni
    
    function __construct($db) {
	$this->_db = $db;
	$res = $this->_db->ExecuteSQL("SELECT COUNT(*) as cnt FROM data_log");
	$this->_countRecords = ($res == false)?0:$res[0]['cnt'];
	$this->_pagePerSiteNumbers = array(10=>10, 30=>30, 50=>50, 100=>100, 'all'=>'all');//Nastaveni moznosti poctu zaznamu na stranku
	$Nok = array(''=>'ALL','OK'=>'OK','NOK'=>'NOK');
	

	//Nastaveni počtu filtru podle sloupcu v databasi
	$this->_filterItems = array(
	    'MeasureTime' => array('czname'=>'čas testu','type'=>'datetime','filter'=>array(),'size'=>30),
	    'InnerCode' =>array('czname'=>'seriové číslo','type'=>'string','filter'=>array(),'size'=>190),
	    'GlobalResult' => array('czname'=>'celk. test','type'=>'string','filter'=>$Nok,'size'=>80),
	    'BulbCurentResult' => array('czname'=>'proud žár. test','type'=>'string','filter'=>$Nok,'size'=>85),
	    'BulbCurentMeasured' => array('czname'=>'proud žár. vysl','type'=>'number','filter'=>array(),'size'=>80),
	    'BulbCurentMeasuredMin' => array('czname'=>'proud žár. min','type'=>'number','filter'=>array(),'size'=>80),
	    'BulbCurentMeasuredMax' => array('czname'=>'proud žár. max','type'=>'number','filter'=>array(),'size'=>81),
	    'BulbVoltageResult' => array('czname'=>'napětí žár. test','type'=>'string','filter'=>$Nok,'size'=>80),
	    'BulbVoltageMeasured' => array('czname'=>'napětí žár. vysl','type'=>'number','filter'=>array(),'size'=>80),
	    'TestLengthMeasured' => array('czname'=>'délka test','type'=>'number','filter'=>array(),'size'=>80),
	    'TestLengthMeasuredMin' => array('czname'=>'délka test min','type'=>'number','filter'=>array(),'size'=>80),
	    'TestLengthMeasuredMax' => array('czname'=>'délka test max','type'=>'number','filter'=>array(),'size'=>81),
	    'TestBlinkShine01' => array('czname'=>'blikání svit1','type'=>'number','filter'=>array(),'size'=>80),
	    'TestBlinkDark01' => array('czname'=>'blikání tma1','type'=>'number','filter'=>array(),'size'=>80),
	    'TestBlinkShine02' => array('czname'=>'blikání svit2','type'=>'number','filter'=>array(),'size'=>80),
	    'TestBlinkDark02' => array('czname'=>'blikání tma2','type'=>'number','filter'=>array(),'size'=>80),
	    'TestBlinkShine03' => array('czname'=>'blikání svit3','type'=>'number','filter'=>array(),'size'=>80),
	    'LeaksResults' => array('czname'=>'těstnost test','type'=>'string','filter'=>$Nok,'size'=>80),
	    'LeaksMeasured' => array('czname'=>'těstnost únik','type'=>'number','filter'=>array(),'size'=>80),
	    'LeaksAllowed' => array('czname'=>'těstnost pov.únik','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureDest' => array('czname'=>'tlakoání natlakovat','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureMin' => array('czname'=>'tlakování min','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureMax' => array('czname'=>'tlakování max','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureActual' => array('czname'=>'tlakování natlakováno','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureTime' => array('czname'=>'čas tlakování','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureMeasuredTime' => array('czname'=>'čas měření','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureAfterDelaysMin' => array('czname'=>'po prodlevě','type'=>'number','filter'=>array(),'size'=>80),
	    'PressureDelays' => array('czname'=>'prodleva','type'=>'number','filter'=>array(),'size'=>80),
	);
	
	if(isset($_GET['sort']))
	    $this->_sort = filter_var($_GET['sort'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
	
	if(isset($_GET['pagepersite']))
	    $this->_pagePerSite = filter_var($_GET['pagepersite'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
	
	if(isset($_GET['pagestart']))
	    $this->_pageStart = filter_var($_GET['pagestart'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
	
	if(isset($_GET['datefrom']))
	    $this->_dateFrom = filter_var($_GET['datefrom'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
	
	if(isset($_GET['dateto'])){
	    $this->_dateTo = filter_var($_GET['dateto'], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
	    if($this->_dateTo!='')$this->_where[] = " MeasureTime BETWEEN '".date("Y-m-d H:i:s",strtotime($this->_dateFrom))."' AND '".date("Y-m-d H:i:s",strtotime($this->_dateTo))."' "; 
	}
	
	if(isset($_GET['filter_select']))
	    $this->_selectedFilterItems = $_GET['filter_select'];
	
	//Cyklus pro naplneni promennych z odeslanych filtru
	foreach ($this->_selectedFilterItems as $item) {
	    if($item == 'multiselect-all')continue;
	    if(isset($_GET[$item])){
		$this->{"_".lcfirst($item)} = filter_var($_GET[$item], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
		if($item == 'InnerCode' && $this->_innerCode !=''){
		    $this->_where[] = $item." LIKE '".$this->{"_".lcfirst($item)}."%'";
		}else{
		    if($this->{"_".lcfirst($item)}!='')$this->_where[] = $item." = '".$this->{"_".lcfirst($item)}."'";
		}
		
	    }
	}
	
	$this->_limit = "LIMIT ".$this->_pageStart.",".$this->_pagePerSite;
	
	/*
	echo "<pre>";
	var_dump($_GET);
	echo "</pre>";
	*/
    }
    
    
    private function ShowFilter(){
	$colsapan = sizeof($this->_selectedFilterItems)+4;
	$out = '';
	//$out .= '<fieldset class="filter_fieldset">';
	//$out .= '<legend class ="filter_legend">FILTR</legend>';
	$out .= '<table class="hovertable">';
	$out .= '<tr>';
	$out .= '   <th align="left" colspan="'.$colsapan.'">'.$this->ShowSelectItemButton().'</th>';
	$out .= '</tr>';
	$out .= '<tr>';
	if(in_array('MeasureTime',$this->_selectedFilterItems))$out .= '<th>'.$this->FilterDateFrom().'</th><th>'.$this->FilterDateTo().'</th>';
	
	
	foreach ($this->_selectedFilterItems as $item) {
	    if($item == 'MeasureTime' || $item == 'multiselect-all')continue;
	    $out .= call_user_func(array($this, 'FilterItemFce'), $item);
	}
	
	$out .= '</tr>';
	$out .= '<tr>';
	$out .= '   <th align="right" colspan="'.$colsapan.'">';
	//$out .= '	<a href="control/exportdata.php?'.$this->GenerateHttpQuery(array(),array()).'">Export CSV</a>';
	$out .= '	<input type="button" onclick="document.location.href=\'graf.php\'" value="graf">';
	$out .= '	<input type="button" onclick="document.location.href=\'control/exportdata.php?'.$this->GenerateHttpQuery(array(),array()).'\'" value="Export do CSV">';
	$out .= '	<input type="button" onclick="resetToDefaults();" value="reset">';
	$out .= '	<input type="submit" value="vyhledat">';
	$out .= '   </th>';
	$out .= '</tr>';
	$out .= '</table>';
	//$out .= '</fieldset>';
	return $out;
    }
    
    private function ShowSelectItemButton(){
	$out = '';
	$out .= '<label for="pagepersite"></label>';
	$out .= '<select class="multiselect" multiple="multiple" name="filter_select[]" id="filter_select">';
		 foreach ($this->_filterItems as $value => $option) {
		    $selected = (in_array($value, $this->_selectedFilterItems))?'selected':'';
	$out .= '   <option value="'.$value.'" '.$selected.'>'.$option['czname'].'</option>';
		 }
	$out .= '</select>';

	$out .= '<script type="text/javascript">
		    $(document).ready(function() {
			$(\'.multiselect\').multiselect({
			    includeSelectAllOption: true,
			    enableFiltering:true
			});
		    });
		</script>';
	$out .= '<input type="button" onclick="nullpagestart();document.filter.submit();return true;" value="nastavit filtr">';
	return $out;
    }
    
    public function showtable(){
	$out = '';
	$out .= '<form name="filter" id="filter" method="get" action="index.php">';
	$out .= $this->ShowFilter();
	$out .= '<br><table class="hovertable">';
	$out .= '	<thead>';
	$out .= '	    <tr class = "hower">';
	$out .= '		<th rowspan="2"></th>';
	$out .= '		<th rowspan="2">čas testu '.$this->ShowSortButton('MeasureTime').'</th>';
	$out .= '		<th rowspan="2">sériové číslo'.$this->ShowSortButton('InnerCode').'</th>';
	$out .= '		<th rowspan="2">celk. test</th>';
	$out .= '		<th colspan="4">proud žárovka (A)</th>';
	$out .= '		<th colspan="2">napětí žárovka (V)</th>';
	$out .= '		<th colspan="3">delka testu  (ms)</th>';
	$out .= '		<th colspan="5">průběh testu blikání</th>';
	$out .= '		<th colspan="3">těstnost (mBar)</th>';
	$out .= '		<th colspan="3">tlakování (mBar)</th>';
	$out .= '		<th colspan="5">tlakování délka testu</th>';
	$out .= '	    </tr>';
	$out .= '	    <tr>';
	$out .= '		<th>test</th>';
	$out .= '		<th>vysl.'.$this->ShowSortButton('BulbCurentMeasured').'</th>';
    	$out .= '		<th>min'.$this->ShowSortButton('BulbCurentMeasuredMin').'</th>';
	$out .= '		<th>max'.$this->ShowSortButton('BulbCurentMeasuredMax').'</th>';
	$out .= '		<th>test</th>';
	$out .= '		<th>vysl.'.$this->ShowSortButton('BulbVoltageMeasured').'</th>';
	$out .= '		<th>vysl.'.$this->ShowSortButton('TestLengthMeasured').'</th>';
    	$out .= '		<th>min'.$this->ShowSortButton('TestLengthMeasuredMin').'</th>';
	$out .= '		<th>max'.$this->ShowSortButton('TestLengthMeasuredMax').'</th>';
	$out .= '		<th>svit'.$this->ShowSortButton('TestBlinkShine01').'</th>';
	$out .= '		<th>tma'.$this->ShowSortButton('TestBlinkDark01').'</th>';
	$out .= '		<th>svit'.$this->ShowSortButton('TestBlinkShine02').'</th>';
	$out .= '		<th>tma'.$this->ShowSortButton('TestBlinkDark02').'</th>';
	$out .= '		<th>svit'.$this->ShowSortButton('TestBlinkShine03').'</th>';
	$out .= '		<th>test</th>';
	$out .= '		<th>únik'.$this->ShowSortButton('LeaksMeasured').'</th>';
	$out .= '		<th>povolený únik'.$this->ShowSortButton('LeaksAllowed').'</th>';
	$out .= '		<th>natlakovat'.$this->ShowSortButton('PressureDest').'</th>';
	$out .= '		<th>min'.$this->ShowSortButton('PressureMin').'</th>';
	$out .= '		<th>max'.$this->ShowSortButton('PressureMax').'</th>';
	$out .= '		<th>natlakováno'.$this->ShowSortButton('PressureActual').'</th>';
	$out .= '		<th>čas tlakování'.$this->ShowSortButton('PressureTime').'</th>';
	$out .= '		<th>čas měření'.$this->ShowSortButton('PressureMeasuredTime').'</th>';
	$out .= '		<th>po prodlevě'.$this->ShowSortButton('PressureAfterDelaysMin').'</th>';
	$out .= '		<th>prodleva'.$this->ShowSortButton('PressureDelays').'</th>';
	$out .= '	    </tr>';
	$out .= '	</thead>';
	
	$out .= $this->GetTableBody();
	$out .= $this->ShowNavigateBar();
	
	$out .= '<table>';
	$out .= '<form>';
	echo $out;
    }
    
    private function GetTableBody(){
	$where = '';
	if(sizeof($this->_where)> 0){
	    $where .= " WHERE ".implode(' AND ',$this->_where); 
	}
	$sort = '';
	if($this->_sort !='')
	    $sort = " ORDER BY $this->_sort";
	    
	$sql ="SELECT * FROM data_log $where $sort $this->_limit;";
	$res = $this->_db->ExecuteSQL($sql);
	$out = '';
	$out .= '<tbody>';
	$int = 1;
	if($this->_db->records > 0){
	foreach ($res as $row) {
	    $out.='<tr>';
	    $out.='<td>'.$int.'</td>';
	    $out.='<td style="width:120px;">'.date("d.m.Y H:i:s",strtotime($row['MeasureTime'])).'</td>';
	    $out.='<td>'.$row['InnerCode'].'</td>';
	    $out.='<td>'.$row['GlobalResult'].'</td>';
	    $out.='<td>'.$row['BulbCurentResult'].'</td>';
	    $out.='<td>'.$row['BulbCurentMeasured'].'</td>';
	    $out.='<td>'.$row['BulbCurentMeasuredMin'].'</td>';
	    $out.='<td>'.$row['BulbCurentMeasuredMax'].'</td>';
	    $out.='<td>'.$row['BulbVoltageResult'].'</td>';
	    $out.='<td>'.$row['BulbVoltageMeasured'].'</td>';
	    $out.='<td>'.$row['TestLengthMeasured'].'</td>';
	    $out.='<td>'.$row['TestLengthMeasuredMin'].'</td>';
	    $out.='<td>'.$row['TestLengthMeasuredMax'].'</td>';
	    $out.='<td>'.$row['TestBlinkShine01'].'</td>';
	    $out.='<td>'.$row['TestBlinkDark01'].'</td>';
	    $out.='<td>'.$row['TestBlinkShine02'].'</td>';
	    $out.='<td>'.$row['TestBlinkDark02'].'</td>';
	    $out.='<td>'.$row['TestBlinkShine03'].'</td>';
	    $out.='<td>'.$row['LeaksResults'].'</td>';
	    $out.='<td>'.$row['LeaksMeasured'].'</td>';
	    $out.='<td>'.$row['LeaksAllowed'].'</td>';
	    $out.='<td>'.$row['PressureDest'].'</td>';
	    $out.='<td>'.$row['PressureMin'].'</td>';
	    $out.='<td>'.$row['PressureMax'].'</td>';
	    $out.='<td>'.$row['PressureActual'].'</td>';
	    $out.='<td>'.$row['PressureTime'].'</td>';
	    $out.='<td>'.$row['PressureMeasuredTime'].'</td>';
	    $out.='<td>'.$row['PressureAfterDelaysMin'].'</td>';
	    $out.='<td>'.$row['PressureDelays'].'</td>';
	    $out.='<tr>';
	    $int++;
	}
	}
	$out .= '</tbody>';
	return $out;
    }
    
    private function ShowNavigateBar(){
	$out =  '';
	$out .= '<tfoot>';
	$out .= '   <tr>';
	$out .= '	<td colspan = "29">';
	$out .= '	    <table class="navigatetable">';
	$out .= '	    <thead>';
	$out .= '		<tr>';
	$out .= '		    <th align="left">'.$this->ShowPrevNext().' '.$this->_pageStart.'/'.$this->_countRecords.'</th>';
	$out .= '		    <th align="right">'.$this->ShowPagePerSite('záznamů na stránku:').'</th>';
	$out .= '		</tr>';
	$out .= '	    </thead>';
	$out .= '	    </table>';
	$out .= '	</td>';
	$out .= '   </tr>';
	$out .= '<tfoot>';
	return $out;
    }
    
    private function ShowPagePerSite($text){
	$out = '';
	$out .='    <label for="pagepersite">'.$text.'</label>';
	$out .= '   <select name="pagepersite" id="pagepersite" onchange="document.filter.submit();return true;">';
	foreach ($this->_pagePerSiteNumbers as $value => $option) {
	    $selected = ($this->_pagePerSite == $option)?"selected":"";
	    $out .= '   <option value="'.$value.'" '.$selected.'>'.$option.'</option>';
	}
	$out .= '   </select>';
	return $out;
    }
    
    private function ShowPrevNext(){
	$out = '';
	$out .='<input type="hidden" name="pagestart" id="pagestart" value="'.$this->_pageStart.'">';
	$out .='<input type="button" value="předchozí strana" onclick="degress('.$this->_pagePerSite.');document.filter.submit();return true;">';
	$out .='<input type="button" value="další strana" onclick="append('.$this->_pagePerSite.');document.filter.submit();return true;">';
	return $out;
    }
    
    private function ShowPrev(){
	$start = (($this->_pageStart-$this->_pagePerSite)<0)?0:($this->_pageStart-$this->_pagePerSite);
	$out = '';
	//$out .='<input type="hidden" name="pagestart" value="'.$start.'">';
	$out .='<input type="button" value="předchozí strana" onclick="document.filter.submit();return true;">';
	return $out;
    }
    
    
    private function FilterDateFrom(){
	$out = '<label for="datefrom">od</label>';
	$out .= '<div  id="datefrom_picker" class="input-append">';
        $out .= '   <input data-format="dd.MM.yyyy hh:mm:ss" type="text" style="width:160px;" id="datefrom" name="datefrom" value="'.$this->_dateFrom.'">';
	$out .= '   <span class="add-on">';
	$out .= '	<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>';
	$out .='    </span>';
	$out .='</div>';
	return $out;
    }
    
    private function FilterDateTo(){
	$out = '<label for="dateto">do</label>';
	$out .= '<div  id="dateto_picker" class="input-append">';
        $out .= '   <input data-format="dd.MM.yyyy hh:mm:ss" type="text" style="width:160px;" id="dateto" name="dateto" value="'.$this->_dateTo.'">';
	$out .= '   <span class="add-on">';
	$out .= '	<i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>';
	$out .='    </span>';
	$out .='</div>';
	return $out;
    }
    
    private function FilterItemFce($item){
	$value = '';
	$out  = '';
	if(isset($_GET[$item]))
	    $value = filter_var($_GET[$item], FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
	
	$out .= '<th>';
	$out .= '<label for="'.$item.'">'.$this->_filterItems[$item]['czname'].'</label>';
	if(sizeof($this->_filterItems[$item]['filter'])>0){
	    $out .= '   <select name="'.$item.'" id="'.$item.'" onchange="document.filter.submit();return true;" style="width:'.$this->_filterItems[$item]['size'].';">';
	    foreach ($this->_filterItems[$item]['filter'] as $value => $option) {
		$selected = ($this->{"_".lcfirst($item)} == $option)?"selected":"";
		$out .= '   <option value="'.$value.'" '.$selected.'>'.$option.'</option>';
	    }
	$out .= '   </select>';
	    
	}else{
	    $out .= '<input type="text" name="'.$item.'" id="'.$item.'" value="'.$value.'" style="width:'.$this->_filterItems[$item]['size'].';">';
	    $out .= '</th>';
	}
	return $out;
    }
    
    private function ShowSortButton($ColumnName){
	$out = "";
	$out .= "<a href='index.php?sort=$ColumnName DESC'><b style='font-size:12px;'>↑</b></a>";
	$out .= "&nbsp;";
	$out .= "<a href='index.php?sort=$ColumnName'><b style='font-size:12px;'>↓</b></a>";
	return $out; 
    }
    
    public function ExportToCSV(){
	$FilteredCols = array('Timestamp');
	
	//create data query
	$where = '';
	if(sizeof($this->_where)> 0){
	    $where .= " WHERE ".implode(' AND ',$this->_where); 
	}
	$sort = '';
	if($this->_sort !='')
	    $sort = " ORDER BY $this->_sort";
	    
	$sql ="SELECT * FROM data_log $where $sort;";
	$res = $this->_db->ExecuteSQL($sql);
	
	
	$filename = 'd:\zal\export-datalog'.date('Ymd-His').'.csv';
	$Delimiter = ';';
	$fp = fopen($filename, "w");	
		
	if ($fp === false)
	    die('FATAL ERROR. PLEASE CONTACT ADMINISTRATOR.');
	
	$EmptyLine = array();
	$Buffer[] = array("File: export-datalog.csv");
	$Buffer[] = array("Generated by DataTable at: ".date("d.m.Y H:i:s"));
	$Buffer[] = $EmptyLine;
	$Buffer[] = $EmptyLine;	
	//$Buffer[] = array('datum od:'.$this->_dateFrom.' - '.$this->_dateTo);
	
	foreach ($Buffer as $BuffLine) {
	    if (fputcsv($fp,$BuffLine,$Delimiter) === false) die('FATAL ERROR. PLEASE CONTACT ADMINISTRATOR.');
	}
	$Buffer = array();
	
	
	$HeaderColBuff = array();
	$LineColBuff = array();
	foreach ($this->_filterItems as $ColName => $ColData) {
	    if (in_array($ColName,$FilteredCols)) continue;
	    $HeaderColBuff[] = $ColData['czname'];
	}
	
	if (fputcsv($fp,$HeaderColBuff,$Delimiter) === false) die('FATAL ERROR. PLEASE CONTACT ADMINISTRATOR.');
	if($res!=false){
	    foreach ($res as $row) {
		$LineColBuff = array();
		foreach ($row as $ColName => $Data) {
		    if (in_array($ColName,$FilteredCols)) continue;
		    if ($ColName != 'MeasureTime') $Data = str_replace('.', ',', $Data);
		    $LineColBuff[] = $Data;
		    
		}
		if (fputcsv($fp,$LineColBuff,$Delimiter) === false) die('FATAL ERROR. PLEASE CONTACT ADMINISTRATOR.');
	    }
	}
	
	header("Cache-Control: cache, must-revalidate");
	header("Pragma: public"); // must be here for IE
	header("Content-Type: application/force-download ; name=\"export-datalog.csv-".date("dmy").".csv\"; charset=UTF-8");
	header("Content-Disposition: attachment; filename=\"export-datalog.csv-".date("dmy").".csv\"");
	fclose($fp);
	echo iconv("UTF-8","WINDOWS-1250",file_get_contents ($filename));
	unlink($filename);
    }
    
    private function GenerateHttpQuery($KeyValues,$NoKeyNames,$Separator='&amp;') {
	$Buff ='';
  	$HttpQuery = http_build_query($KeyValues, '', $Separator);

	$NoKeyNames = array_merge(array_keys($KeyValues),$NoKeyNames);
	if (($OtherRequestParameters = $this->GetRequestParameters($NoKeyNames,$Separator))) {
	    if ($HttpQuery)
		$Buff .= $HttpQuery.$Separator; 
		$Buff .= $OtherRequestParameters;
	    } else 
		$Buff .= $HttpQuery;
	    return $Buff;
  	}
	
    private function GetRequestParameters($NoKeyNames,$Separator = '&amp;') {
	$buffarr =  array();
  	foreach ($_GET as $key=>$Value) {
	    if (!in_array($key,$NoKeyNames))  	
		$buffarr[$key] = $Value;
	    }
	    if (sizeof($buffarr))
	  	return http_build_query($buffarr, '', $Separator);
	
	else return false;
    }
  
    public function GetInnerCode($id){
	$data = array();
	$result = array();
	$query="SELECT InnerCode FROM data_log WHERE InnerCode LIKE '$id%' LIMIT 30;";
	$res = $this->_db->ExecuteSQL($query);
	if($this->_db->records>0){
	    foreach ($res as $value)
		$data[]=$value['InnerCode'];
	}
 
	$result['data'] = $data;
	$result['query'] = $id;
	$result['suggestions'] = $data;
	return json_encode($result);
    }
    
    
}

