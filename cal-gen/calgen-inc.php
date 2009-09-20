<?php

function calgen_weekdays($date_text)
{
	$date_timestamp = strtotime($date_text);
	$date_array = getdate($date_timestamp);
	for ($index = 0; $index < 7; $index++)
	{
		$weekday_timestamp = 
			mktime(0, 0, 0, 
			$date_array['mon'],
			$date_array['mday'] - $date_array['wday'] + 1 + $index,
			$date_array['year']);
	
		$weekdays[$index] = getdate($weekday_timestamp);
	}
	return $weekdays;
}

function calgen_week_generate_col1($weekdays)
{
	$text .= "<table border='1'>";
	foreach ($weekdays as $index => $weekday)
	{
		$text .= 
			"<tr>" .
				"<td>" .
					"{$index}:{$weekday['weekday']} " .
					"[" . date("Y-m-d", $weekday[0]) . "]" .
				"</td>" .
			"</tr>";
	}
	$text .= "</table>";
	return $text;
}

?>
