<?php

/**
 *	Tags Unique
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
 *	Webpage: http://code.google.com/p/boyanov/wiki/TagsUnique
 *	Source: http://code.google.com/p/boyanov/source/browse?repo=prototypes#hg/tagsunique
 *
 **/

	// ------------------------------------------------------------------------

	// Global variables
	$TAGSUNIQUE_TAGS_BASEDIR = "tags";
	$TAGSUNIQUE_TAG_CHARACTERS = 
		// "ABCDEFGHIJKLMNOPQRSTUVWXYZ" . // NOTE: Under MS Windows only one case should be used.
		"abcdefghijklmnopqrstuvwxyz" .	
		"0123456789";
		/*
			// All valid characters - upper and lower case
			ABCDEFGHIJKLMNOPQRSTUVWXYZ
			abcdefghijklmnopqrstuvwxyz
			0123456789
			// All unique shapes
			ABDEFHJKMNPRSTXZ
			abdefhjkmnprstxz
			23456789
		*/
	// ------------------------------------------------------------------------

	function tagsunique_random_character()
	{
		global $TAGSUNIQUE_TAG_CHARACTERS;
		$length = strlen($TAGSUNIQUE_TAG_CHARACTERS);
		$count = $length - 1;
		// Note: the function mt_rand() works much faster thatn the rand() function.
		$rand = mt_rand(0, $count);
		$character = $TAGSUNIQUE_TAG_CHARACTERS[$rand];
		// /*DEBUG*/ print "tagsunique_random_character[0,{$count}]={$rand}/{$character}<br />";
		return $character;
	}
	
	// ------------------------------------------------------------------------

	function tagsunique_tag_to_path($tag)
	{
		for ($index = 0; $index < strlen($tag) - 1; $index++)
		{
			// print "$tag[$index] ";
			$path .= $tag[$index] . '/';
		}
		$path .= $tag[$index];
		return $path;
	}
	
	// ------------------------------------------------------------------------
	
	function tagsunique_tag_exists($tag)
	{
		global $TAGSUNIQUE_TAGS_BASEDIR;
		$tag_path = tagsunique_tag_to_path($tag);
		$exists = file_exists($TAGSUNIQUE_TAGS_BASEDIR . '/' . $tag_path);
		return $exists;
	}
	
	// ------------------------------------------------------------------------
	
	function tagsunique_tag_create($tag, $final = TRUE)
	{
		global $TAGSUNIQUE_TAGS_BASEDIR;
		// print "tag: {$tag}<br />";
		if (!tagsunique_tag_exists($tag))
		{
			if ($final)
			{
				$node_final = substr($tag, -1);
				// print "node_final: {$node_final}<br />";
				$folder = substr($tag, 0, -1);
				// print "folder: {$folder}<br />";
				if (strlen($folder) != 0) 
				{
					$path = tagsunique_tag_to_path($folder);
					// print "path: {$path}<br />";
					$path_mkdir = $TAGSUNIQUE_TAGS_BASEDIR . '/' . $path;
					// print "path_mkdir: {$path_mkdir}<br />";
					if (!file_exists($path_mkdir))
					{
						mkdir($path_mkdir, 0700, TRUE);
						// todo: the case when mkdir fails should be handled or at least logged.
					}
					// todo: find out if "file_exists" could return "true" in the above statement.
				}
				$path_node_final = $TAGSUNIQUE_TAGS_BASEDIR . '/' . $path . '/' . $node_final;
				// print "path_node_final: {$path_node_final}<br />";
				if (!file_exists($path_node_final))
				{
					$result = touch($path_node_final);
					// todo: the case when touch fails should be logged.
				}
				// todo: find out if "file_exists" could return "true" in the above statement.
			}
			else
			{
				$folder = $tag;
				// print "folder: {$folder}<br />";
				$path = tagsunique_tag_to_path($folder);
				// print "path: {$path}<br />";
				$path_mkdir = $TAGSUNIQUE_TAGS_BASEDIR . '/' . $path;
				// print "path_mkdir: {$path_mkdir}<br />";
				if (!file_exists($path_mkdir))
				{
					$result = mkdir($path_mkdir, 0700, TRUE);
					// todo: the case when mkdir fails should be logged.
				}
				// todo: find out if "file_exists" could return "true" in the above statement.
			}
		}
		else
		{
			$result = FALSE;
			// todo: should be logged.
		}
		return $result;
	}
	
	// ------------------------------------------------------------------------

	function tagsunique_tag_is_final($tag)
	{
		global $TAGSUNIQUE_TAGS_BASEDIR;
		$path = tagsunique_tag_to_path($tag);
		$tag_path = $TAGSUNIQUE_TAGS_BASEDIR . '/' . $path;
		if (file_exists($tag_path))
		{
			$result = filetype($tag_path) == "file";
		}
		else
		{
			$result = NULL;
		}
		return $result;
	}

	// ------------------------------------------------------------------------
	
	function tagsunique_tag_generate($tag_sup = "", $final = TRUE)
	{
		// The parameters "tag_sup" and "final" are for internal use only for the recursion.
		// print "-->>-- tagsunique_tag_generate ( tag_sup='{$tag_sup}', final=" . var_export($final, TRUE) . " )<br />";
		static $depth;
		if ($tag_sup === "" && $final === TRUE)
			$depth = 0;
		else
			$depth++;
		if ($depth > 100)
		{
			print "<br />tagsunique_tag_generate - failed/collision (depth:{$depth})<br />\n";
			exit("failed: " . __FILE__ . "#" . __LINE__ . "/" . __FUNCTION__);
			// Todo: This MUST be logged.
		}
		$char = tagsunique_random_character();
		$tag = $tag_sup . $char;
		$tag_exists = tagsunique_tag_exists($tag);
		// print "tag: {$tag} (char: {$char}) tag_exists: " . var_export($tag_exists, TRUE) . "<br />";
		if ($tag_exists)
		{
			if (tagsunique_tag_is_final($tag))
			{
				// tag is final (i.e. is file)
				// then try to find/create non-final tag (i.e. a folder)
				$tag_sub = tagsunique_tag_generate($tag_sup, FALSE);
				// ... and create new final tag (i.e. a file) in this folder
				if (tagsunique_tag_is_final($tag_sub))
				{
					$tag = $tag_sub;
				}
				else
				{
					$tag = tagsunique_tag_generate($tag_sub, TRUE);
				}
			}
			else
			{
				// tag is not final (i.e. is folder)
				// then try to create new final tag (i.e. a file)
				$tag = tagsunique_tag_generate($tag, TRUE);
			}
		}
		else
		{
			$tag_created = tagsunique_tag_create($tag, $final);
			// print "CREATED (final={$final}) tag: {$tag}<br />";
		}
		// print "--<<-- tagsunique_tag_generate tag='{$tag}'<br />";
		// /*DEBUG*/ if ($tag_created) print "<br />tag created: {$tag}"; else print "^";
		// /*DEBUG*/ if ($tag_sup === "" && $final === TRUE) print "<br />tagsunique_tag_generate - finished ({$depth})<br />";
		return $tag;
	}

	// ------------------------------------------------------------------------

?>
