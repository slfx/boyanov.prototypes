<?php

/**
 *	FNV Hash
 *
 *  Author: Neven Boyanov
 *  Copyright (c) 2009 by Neven Boyanov (Boyanov.Org)
 *  Licensed under GNU/GPLv2 - http://www.gnu.org/licenses/
 *
 *  This program is distributed under the terms of the License,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty
 *  of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 *  the License for more details.
 *
 **/

require_once "fnvhash-inc.php";

	print "<code>";
	// $text = "test";
	$text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin at libero mi, quis luctus massa.";
	print "text: '{$text}'<br />";
	$hash = fnvhash_fnv1($text);
	print "hash: " . sprintf("%X", $hash) . " (" . sprintf("%u", $hash) . ")<br />";
	print "</code>";

?>
