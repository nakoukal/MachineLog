<?php
include("pChart/class/pData.class.php");
include("pChart/class/pDraw.class.php");
include("pChart/class/pImage.class.php");
include_once('class/class.MySQL.php');
include_once('globals.php');


$LibPath = 'pChart/';
$Graphwidth = 500;
$Graphheigth = 260;
$GraphLabel = 'Vysledky testu za 10 dnu';
$myData = new pData();
$myData->loadPalette($LibPath."palettes/psma.color", TRUE);
$oMySQL = new MySQL($dbName, $dbUser, $dbPasswd, $dbHost, $dbPort);
$sql = "
((SELECT tok.den,tok.ok,tnok.nok FROM 
(SELECT DATE(MeasureTime) den, COUNT(*) ok FROM data_log WHERE GlobalResult = 'OK' GROUP BY den)AS tok 
 LEFT JOIN
(SELECT DATE(MeasureTime) den, COUNT(*) nok FROM data_log WHERE GlobalResult = 'NOK' GROUP BY den)AS tnok
ON tok.den = tnok.den))

UNION DISTINCT

((SELECT tnok.den,tok.ok,tnok.nok FROM 
(SELECT DATE(MeasureTime) den, COUNT(*) nok FROM data_log WHERE GlobalResult = 'NOK' GROUP BY den)AS tnok
 LEFT JOIN
(SELECT DATE(MeasureTime) den, COUNT(*) ok FROM data_log WHERE GlobalResult = 'OK' GROUP BY den)AS tok 
ON tok.den = tnok.den))
ORDER BY den
LIMIT 10;
;    
";

$Result = $oMySQL->ExecuteSQL($sql);
foreach ($Result as $row) {
    $myData->addPoints(date('d.m.',strtotime($row['den'])),"time");
    $myData->addPoints($row['ok'],"ok");
    $myData->addPoints($row['nok'],"nok");
}
	

$myData->setSerieWeight("ok",1);
$myData->setAxisName(0,"Global Result");
//$myData->setAxisUnit(0,"OK,NOK");

/* Create the abscissa serie */
$myData->setAbscissa("time");//set the x line
//$myData->setAbscissaName("Den");
//$myData->setXAxisDisplay(AXIS_FORMAT_DATE);
$myPicture = new pImage($Graphwidth,$Graphheigth,$myData);
 
$Settings = array("R"=>240, "G"=>240, "B"=>240);
$myPicture->setFontProperties(array("FontName"=>$LibPath."fonts/verdana.ttf","FontSize"=>8));
$myPicture->drawFilledRectangle(0,0,$Graphwidth,$Graphheigth,$Settings);
$myPicture->drawRectangle(0,0,$Graphwidth,$Graphheigth,array("R"=>0,"G"=>0,"B"=>0));
$myPicture->drawRectangle(0,0,$Graphwidth-1,$Graphheigth-1,array("R"=>0,"G"=>0,"B"=>0)); 
$myPicture->setShadow(FALSE);
$myPicture->setGraphArea(50,30,$Graphwidth-60,$Graphheigth-50);

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));
$myPicture->setFontProperties(array("FontName"=>$LibPath."fonts/verdana.ttf","FontSize"=>8));

$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

$Settings = array("Mode"=>SCALE_MODE_ADDALL_START0
		,"DrawSubTicks"=>TRUE
		,"DrawArrows"=>TRUE
		,"ArrowSize"=>6
		,"Pos"=>SCALE_POS_LEFTRIGHT
		, "LabelSkip"=>0
		, "SkippedInnerTickWidth"=>10
		, "LabelingMethod"=>LABELING_ALL
		, "GridR"=>0, "GridG"=>0, "GridB"=>0, "GridAlpha"=>20
		, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>20
		, "LabelRotation"=>90, "DrawXLines"=>1, "DrawSubTicks"=>0, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>20, "DrawYLines"=>ALL);


$myPicture->drawScale($Settings);

$Config = array("DisplayValues"=>1, "AroundZero"=>0,"BreakVoid"=>1,"RecordImageMap"=>FALSE);

$GraphType = 'StackedBarChart';
switch ($GraphType) {
    case 'AreaChart':
	$myPicture->drawAreaChart($Config);
	break;
    case 'FilledSplineChart':
	$myPicture->drawFilledSplineChart($Config);
	break;
    case 'SplineChart':
	$myPicture->drawSplineChart($Config);
	break;
    case 'BarChart':
	$myPicture->drawBarChart($Config,'tetimes');
	break;
    case 'StackedBarChart':
	$myPicture->drawStackedBarChart($Config);
	break;
    case 'PlotChart':
	$myPicture->drawPlotChart($Config);
	break;

    default:
	break;
}
$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>$LibPath."fonts/verdana.ttf", "FontSize"=>8, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER, "Mode"=>LEGEND_VERTICAL);
$myPicture->drawLegend($Graphwidth-65,40,$Config);

$myPicture->setFontProperties(array("FontName"=>$LibPath."fonts/verdana.ttf","FontSize"=>10));
$TextSettings = array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE, "R"=>0, "G"=>0, "B"=>0);
$myPicture->drawText(200,15,$GraphLabel,$TextSettings);

$myPicture->stroke();