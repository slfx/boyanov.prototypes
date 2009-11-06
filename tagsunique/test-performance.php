<?php
print "<code>";
print "started: " . microtime(TRUE) . "<br />";

require_once "tagsunique-inc.php";

	// $TAGSUNIQUE_TAG_CHARACTERS = "1234abcd";
	$TAGSUNIQUE_TAG_CHARACTERS = "ABDEFHJKMNPRSTXZ23456789";

	// Test
	print "-- Test tagsunique_tag_generate<br />";
	
	$a_max = 5;	// number of packages
	$b_max = 10;	// itterations per package
	$t_max = $a_max * $b_max;
	if ($t_max > 500) set_time_limit($t_max / 10);
	for ($a = 0; $a < $a_max; $a++)
	{
		$performance_mt1 = microtime(TRUE);
		for ($b = 0; $b < $b_max; $b++)
		{
			$tag = tagsunique_tag_generate();
			$tags[] = $tag;
		}
		$performance_mt2 = microtime(TRUE);
		$performance_mt = $performance_mt2 - $performance_mt1;
		$performance[] = $performance_mt;
	}

	print "---- TAGS --------<br />";
	foreach ($tags as $index => $tag)
	{
		// print "tags[{$index}] = {$tag}<br />";
		print "{$tag} ";
	}
	print "<br />";

	print "---- PERFORMANCE --------<br />";
	foreach ($performance as $index => $mt)
	{
		print "performance[{$index}] = {$mt}<br />";
	}

	print "---- PERFORMANCE CHART --------<br />";
	include_once "graphs.inc.php";
	// $k = (1 / $performance[0]) * 100;
	foreach ($performance as $index => $mt)
	{
		$graphs_labels .= "{$index},";
		$graphs_values .= "{$mt},";
	}
	$graph = new BAR_GRAPH("hBar");
	$graph->showValues = 2;
	// $graph->baseValue = 100;
	$graph->values = $graphs_values;
	$graph->labels = $graphs_labels;
	// Format
	$graph->barWidth = 10;
	$graph->barLength = 1;
	$graph->labelSize = 10;
	$graph->absValuesSize = 10;
	$graph->percValuesSize = 10;
	$graph->graphPadding = 20;
	$graph->graphBGColor = "#000000";
	$graph->graphBorder = "2px solid #00cc00";
	$graph->barColors = "#333333";
	$graph->barBGColor = "#666666";
	$graph->barBorder = "2px solid #00cc00";
	$graph->labelColor = "#cccccc";
	$graph->labelBGColor = "#000000";
	$graph->labelBorder = "1px solid #009900";
	$graph->absValuesColor = "#ffffff";
	$graph->absValuesBGColor = "#333333";
	$graph->absValuesBorder = "1px solid #00cc00";
	// Generate graph HTML
	print $graph->create() . "<br />\n\n\n\n\n\n\n\n";

print "finished: " . microtime(TRUE) . "<br />";
print "</code>";
?>
