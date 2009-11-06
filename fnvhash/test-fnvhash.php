<?php

require_once "fnvhash-inc.php";

	print "<code>";
	// $text = "test";
	$text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin at libero mi, quis luctus massa.";
	print "text: '{$text}'<br />";
	$hash = fnvhash_fnv1($text);
	print "hash: " . sprintf("%X", $hash) . " (" . sprintf("%u", $hash) . ")<br />";
	print "</code>";

?>
