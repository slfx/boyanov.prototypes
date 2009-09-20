<?php
print "<code>";


require_once "tagsunique-inc.php";


	
	print "TAGSUNIQUE_TAG_CHARACTERS: " . var_export($TAGSUNIQUE_TAG_CHARACTERS, TRUE) . "<br />";

	/*

	// Test
	print "-- Test tagsunique_random_character<br />";
	$tagsunique_random_character = tagsunique_random_character();
	print "tagsunique_random_character: {$tagsunique_random_character}<br />";
	
	// Test
	print "-- Test tagsunique_tag_to_path<br />";
	$tag_test = "2AC4";
	$path_test = tagsunique_tag_to_path($tag_test);
	print "tag_test: {$tag_test} ==> path_test: {$path_test}<br />";
	
	// Test
	print "-- Test tagsunique_tag_exists<br />";
	$tag_test = "A4";
	$tag_exists_test = tagsunique_tag_exists($tag_test);
	print "tag_exists_test: " . var_export($tag_exists_test, TRUE) . "<br />";
	
	// Test
	print "-- Test tagsunique_tag_create<br />";
	$tag_test = "A2B3";
	tagsunique_tag_create($tag_test);
	$tag_test = "A2B4";
	tagsunique_tag_create($tag_test, FALSE);

	// Test
	print "-- Test tagsunique_tag_is_final<br />";
	$tag_test = "A2B";
	print "tag_is_final({$tag_test}): " . var_export(tagsunique_tag_is_final($tag_test), TRUE) . "<br />";
	$tag_test = "A2B3";
	print "tag_is_final({$tag_test}): " . var_export(tagsunique_tag_is_final($tag_test), TRUE) . "<br />";
	$tag_test = "A2B3C";
	print "tag_is_final({$tag_test}): " . var_export(tagsunique_tag_is_final($tag_test), TRUE) . "<br />";

	*/

	// Test
	print "-- Test tagsunique_tag_generate<br />";

	$counter_max = 200;
	if ($counter_max > 1000) set_time_limit($counter_max / 30);
	for ($counter = 0; $counter < $counter_max; $counter++)
	{
		// print "---- tag[{$counter}] --------<br />";
		$tag = tagsunique_tag_generate();
		// print "tag: {$tag}<br />";
		$tags[] = $tag;
	}
	
	print "---- TAGS --------<br />";
	foreach ($tags as $index => $tag)
	{
		// print "tags[{$index}] = {$tag}<br />";
		print "{$tag} ";
	}


print "</code>";
?>
