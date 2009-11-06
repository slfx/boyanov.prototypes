<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Cal-gen</title>
	<style type="text/css">

html,body
{
	font-family: Georgia;
	font-size: 9pt;
	margin:0;
	padding:0;
	height:100%;
	border:none;
}

table.weekdays
{
	margin: 0px;
	padding: 0px;
	border: none;
	width: 100%;
	height: 100%;
}

td.weekdays-header
{
	padding: 0px;
	border: 1px solid #000000;
	vertical-align: top;
}

td.weekdays
{
	padding: 0px;
	border: 1px solid #000000;
	/*
	border-top-color: #cccccc;
	border-left-color: #aaaaaa;
	border-right-color: #666666;
	border-bottom-color: #000000;
	*/
	vertical-align: top;
}

div.weekdays-header
{
	color: #ffffff;
	font-size: 12pt;
	font-weight: bold;
	margin: 0px;
	padding: 4px;
	background: #bbbbbb;
	border-bottom: 1px solid #000000;
}

div.weekdays-header-left
{
	text-align: left;
	float: left;
}

td.weekdays-footer
{
	color: #000000;
	font-family: Arial;
	font-size: 8pt;
	padding: 2px;
	border: 1px solid #000000;
	vertical-align: bottom;
}

div.weekdays-header-right
{
	text-align: right;
}

div.weekday-header
{
	margin: 0px;
	padding: 2px;
	padding-left: 6px;
	padding-right: 6px;
	clear: none;
	background: #eeeeee;
	border-bottom: 1px dotted #999999;
}

div.weekday-weekday
{
	text-align: left;
	float: left;
}

div.weekday-date
{
	text-align: right;
}

	</style>
</head>
<body>
<?php

require_once "calgen-inc.php";

	// $date_text = "2009-09-21";
	$date_text = date("Y-m-d");
	$weekdays = calgen_weekdays($date_text);
	// print "weekdays: " . var_export($weekdays, TRUE) . "<br />";

	$text = calgen_week_generate_col1($weekdays);
	// print "Week:<br />{$text}";
	
function calgen_week_generate_col2_weekday($weekday)
{
	$text =
		"<div class='weekday-header'>" .
			"<div class='weekday-weekday'>{$weekday['weekday']}</div>" .
			"<div class='weekday-date'>" . date("Y-m-d", $weekday[0]) . "</div>" .
		"</div>" .
		"";	
	return $text;
}

function calgen_week_generate_col2($weekdays)
{
	$text .= 
		"<table class='weekdays'>" .
			"<tr>" .
				"<td colspan='2' class='weekdays-header' style='height: 20%;'>" .
					"<div class='weekdays-header'>" .
						"<div class='weekdays-header-left'>" .
							"[Y:" . date("Y", $weekdays[0][0]) . "/W:" . date("W", $weekdays[0][0]) . "]" .
						"</div>" .
						"<div class='weekdays-header-right'>" .
							date("Y-m-d", $weekdays[0][0]) . " - " . date("Y-m-d", $weekdays[6][0]) . 
						"</div>" .
					"</div>" .
				"</td>" .
			"</tr>" .
			"<tr>" .
				"<td class='weekdays' style='height: 25%;'>" .
					calgen_week_generate_col2_weekday($weekdays[0]) .
				"</td>" .
				"<td class='weekdays'>" .
					calgen_week_generate_col2_weekday($weekdays[3]) .
				"</td>" .
			"</tr>" .
			"<tr>" .
				"<td class='weekdays' style='height: 25%;'>" .
					calgen_week_generate_col2_weekday($weekdays[1]) .
				"</td>" .
				"<td class='weekdays'>" .
					calgen_week_generate_col2_weekday($weekdays[4]) .
				"</td>" .
			"</tr>" .
			"<tr>" .
				"<td rowspan='2' class='weekdays' style='height: 25%;'>" .
					calgen_week_generate_col2_weekday($weekdays[2]) .
				"</td>" .
				"<td class='weekdays'>" .
					calgen_week_generate_col2_weekday($weekdays[5]) .
				"</td>" .
			"</tr>" .
			"<tr>" .
				"<td class='weekdays'>" .
					calgen_week_generate_col2_weekday($weekdays[6]) .
				"</td>" .
			"</tr>" .
			"<tr>" .
				"<td colspan='2' class='weekdays-footer' style='height: 5%;'>" .	// NOTE: For IE6 the total %'s should be less that 100%.
					"" .
					"Form: Worksheet-Weekly, by AppletWorks &trade;.Copyright &copy; 2009 by AppletWorks&trade;" .
				"</td>" .
			"</tr>" .
		"</table>";
	return $text;
}

	$text = calgen_week_generate_col2($weekdays);
	print 
		"<div style='height: 100%;'>" .
		"{$text}" .
		"</div>";

?>
</body>
</html>
