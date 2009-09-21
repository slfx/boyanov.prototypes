<?php


// ----------------------------------------------------------------------------

	function search_variable($search_variables, $search_name)
	{
		$search_index = NULL;
		// print "search_variable -- ";	//	DEBUG
		if ($search_variables)
		{
			foreach ($search_variables as $search_variable_index => $search_variable)
			{
				// print " {$search_variable_index}/{$search_variable} {$search_variable['name']} ";	//	DEBUG
				if ($search_variable['name'] == $search_name)
				{
					// print "(match)";	//	DEBUG
					$search_index = $search_variable_index;
					break;
				}
			}
		}
		// print "<br />";	//	DEBUG
		return $search_index;
	}
	
	function fileini_parse($file_name)
	{
		$file = @fopen($file_name, "r");
		if ($file)
		{
			// print "file: {$file_name}<br />";	// DEBUG
			$blocks_index = 1;
			$config = array();
			$section_name = NULL;
			$description = NULL;
			while (!feof($file))
			{
				$line = fgets($file);
				// print "line: " . var_export($line, true) . "<br />";	// DEBUG
	
				// SECTIONS
				$section_regex = '/^' . '\[(\w*)\]' . '/';
				$result = preg_match($section_regex, $line, $matches);
				if ($result)
				{
					// print "section -- matches({$section_regex}): " . var_export($matches, true) . "<br />";	// DEBUG
					$section_name = trim($matches[1]);
					$section_index = $blocks_index++;
					// print "SECTION -- name[{$section_index}]:{$section_name}<br />";	// DEBUG
					$config['sections'][$section_index]['name'] = $section_name;
					
					continue;
				}
				
				if ($section_name)
				{
					$config_base = &$config['sections'][$section_index];
				}
				else
				{
					$config_base = &$config;
				}
	
				// VARIABLE CURRENT
				$variablecurrent_regex = '/^' . '(\w*)=(.*)' . '/';
				$result = preg_match($variablecurrent_regex, $line, $matches);
				if ($result)
				{
					// print "variablecurrent -- matches({$variablecurrent_regex}): " . var_export($matches, true) . "<br />";	// DEBUG
					$variable_name = trim($matches[1]);
					$variable_value = trim($matches[2]);
					
					$search_variables = $config_base['variables'];
					$search_index = search_variable($search_variables, $variable_name);
					// print "search_index: {$search_index}<br />";
					if ($search_index)
						$variable_index = $search_index;
						else $variable_index = $blocks_index++;
					
					$config_base['variables'][$variable_index]['name'] = $variable_name;
					$config_base['variables'][$variable_index]['value'] = $variable_value;
					// print "VARIABLE CURRENT-- name[{$variable_index}]:{$variable_name} value:{$variable_value}<br />";	// DEBUG
					if ($description)
					{
						$description_slashes = addcslashes(trim($description), "\n");
						$config_base['variables'][$variable_index]['description'] = $description_slashes;
						$description = NULL;
					}
					
					continue;
				}

				// VARIABLE DEFAULT
				$variabledefault_regex = '/^' . '# (\w*)=(.*)' . '/';
				$result = preg_match($variabledefault_regex, $line, $matches);
				if ($result)
				{
					// print "variabledefault -- matches({$variabledefault_regex}): " . var_export($matches, true) . "<br />";	// DEBUG
					$variable_name = trim($matches[1]);
					$variable_value = trim($matches[2]);
					
					$search_variables = $config_base['variables'];
					$search_index = search_variable($search_variables, $variable_name);
					// print "search_index: {$search_index}<br />";
					if ($search_index)
						$variable_index = $search_index;
					else
						$variable_index = $blocks_index++;

					// print "VARIABLE DEFAULT -- name[{$variable_index}]:{$variable_name} value:{$variable_value}<br />";	// DEBUG
					$config_base['variables'][$variable_index]['name'] = $variable_name;
					$config_base['variables'][$variable_index]['default'] = $variable_value;
					
					continue;
				}				
				
				// VARIABLE ENUM
				$variableenum_regex = '/^' . '# (\w*):(.*)' . '/';
				$result = preg_match($variableenum_regex, $line, $matches);
				if ($result)
				{
					// print "variableenum -- matches({$variableenum_regex}): " . var_export($matches, true) . "<br />";	// DEBUG
					$variable_name = trim($matches[1]);
					$variable_value = trim($matches[2]);
					$variable_value_split = split(",", $variable_value);
					
					$search_variables = $config_base['variables'];
					$search_index = search_variable($search_variables, $variable_name);
					// print "search_index: {$search_index}<br />";
					if ($search_index)
						$variable_index = $search_index;
					else
						$variable_index = $blocks_index++;
	
					// print "VARIABLE ENUM -- name[{$variable_index}]:{$variable_name} value:{$variable_value}<br />";	// DEBUG
					$config_base['variables'][$variable_index]['name'] = $variable_name;
					$config_base['variables'][$variable_index]['enum-raw'] = $variable_value;
					$config_base['variables'][$variable_index]['enum'] = $variable_value_split;
					
					continue;
				}
	
				// DESCRIPTION
				$description_regex = '/^' . '#\s+(.*)' . '/';
				$result = preg_match($description_regex, $line, $matches);
				if ($result)
				{
					// print "description -- matches({$description_regex}): " . var_export($matches, true) . "<br />";	// DEBUG
					$description_text = trim($matches[1]);
					// $description_text = addcslashes($description_text . "\n", "\n");
					$description .= $description_text . "\n";
				}
				
				if (trim($line) == '')
				{
					// print "NEW-LINE<br />";	// DEBUG
					if ($description)
					{
						$description_slashes = addcslashes(trim($description), "\n");
						$description_index = $blocks_index++;
						if ($section_name)
						{
							if ($config['sections'][$section_index]['description'])
							{
								$config['sections'][$section_index]['descriptions'][$description_index] = $description_slashes;
								$description = NULL;
							}
							else
							{
								$config['sections'][$section_index]['description'] = $description_slashes;
								$description = NULL;
							}
						}
						else
						{
							$config['descriptions'][$description_index] = $description_slashes;
							$description = NULL;
						}
					}
				}
	
			}
		}
		else
		{
			$config = NULL;
		}
		return $config;
	}

// ----------------------------------------------------------------------------

	define ("FORMBUILD_PARAMETERS_PREFIX", "ctp");
	
	function formbuild_variable_parameter_name($variable_name, $variable_index, $section_name, $section_index, $name)
	{
		/*
		$parameter_name = 
			(FORMBUILD_PARAMETERS_PREFIX ? FORMBUILD_PARAMETERS_PREFIX . "[{$variable_index}]::" : "[{$variable_index}]") .
			($section_name ? "{$section_name}.{$variable_name}" : $variable_name);
		*/
		$parameter_name = 
			(FORMBUILD_PARAMETERS_PREFIX ? FORMBUILD_PARAMETERS_PREFIX . "_" : "") . "config" .
			($section_name && $section_index ? "[sections][{$section_index}]" : "") .
			"[variables]" .
			"[{$variable_index}][{$name}]" .
			"";
		return $parameter_name;
	}
	
	function formbuild_variable_parameter_id($parameter_name)
	{
		static $index = 1;
		$parameter_id = $parameter_name . ':id' . $index++;
		return $parameter_id;
	}
	
	function formbuild_description_parameter_name($description_index, $section_name, $section_index)
	{
		$parameter_name = 
			(FORMBUILD_PARAMETERS_PREFIX ? FORMBUILD_PARAMETERS_PREFIX . "_" : "") . "config" . 
			($section_name && $section_index ? "[sections][{$section_index}]" : "") .
			"[descriptions]" . 
			"[{$description_index}]" .
			"";
		return $parameter_name;
	}
	
	function formbuild_section_parameter_name($section_name, $section_index, $name)
	{
		$parameter_name = 
			(FORMBUILD_PARAMETERS_PREFIX ? FORMBUILD_PARAMETERS_PREFIX . "_" : "") . "config" . 
			"[sections][{$section_index}][{$name}]" . 
			"";
		return $parameter_name;
	}
	
	function formbuild_variable_parameter_parse()
	{
	}

	function formbulid_variable(
		$variable, $variable_index, 
		$section_name = NULL, $section_index = 0,
		$choice_manual = FALSE, $choice_select = FALSE,
		$choice_select_type = "select")
	{
		$variable_name = $variable['name'];
		$variable_enumraw = $variable['enum-raw'];
		$variable_enum = $variable['enum'];
		$variable_value = $variable['value'];
		if (isset($variable['default'])) $variable_default = $variable['default'];
		$variable_description = $variable['description'];
		$variable_description_parameter_value = addcslashes($variable_description, "\n");
		$variable_description = stripcslashes($variable_description);
		$variable_description = nl2br($variable_description);
		
		//
		$vaiable_parameter_name = formbuild_variable_parameter_name($variable_name, $variable_index, $section_name, $section_index, "name");
		// $text .= "vaiable_parameter_name: {$vaiable_parameter_name}<br />";	// DEBUG
		$text .= "<input type='hidden' name='{$vaiable_parameter_name}' value='{$variable_name}' />\n";
		//
		$vaiable_parameter_enumraw = formbuild_variable_parameter_name($variable_name, $variable_index, $section_name, $section_index, "enum-raw");
		// $text .= "vaiable_parameter_enumraw: {$vaiable_parameter_enumraw}<br />";	// DEBUG
		$text .= "<input type='hidden' name='{$vaiable_parameter_enumraw}' value='{$variable_enumraw}' />\n";
		//
		if (isset($variable_default))
		{
			$vaiable_parameter_default = formbuild_variable_parameter_name($variable_name, $variable_index, $section_name, $section_index, "default");
			// $text .= "vaiable_parameter_default: {$vaiable_parameter_default}<br />";	// DEBUG
			$text .= "<input type='hidden' name='{$vaiable_parameter_default}' value='{$variable_default}' />\n";
		}
		//
		$vaiable_parameter_value = formbuild_variable_parameter_name($variable_name, $variable_index, $section_name, $section_index, "value");
		// $text .= "vaiable_parameter_value: {$vaiable_parameter_value}<br />";	// DEBUG
		// $text .= "<input type='hidden' name='{$vaiable_parameter_value}' value='{$variable_value}' />\n";	// IMPORTANT - This must not be here.
		//
		$vaiable_parameter_description = formbuild_variable_parameter_name($variable_name, $variable_index, $section_name, $section_index, "description");
		// $text .= "vaiable_parameter_description: {$vaiable_parameter_description}<br />";	// DEBUG
		$text .= "<input type='hidden' name='{$vaiable_parameter_description}' value='{$variable_description_parameter_value}' />\n";
		//
		$choice_name = $vaiable_parameter_value;
		$text .= "<div style='margin: 4px; padding: 4px; background: #f0f0f0; border: 1px solid #999999;'>";
		$text .= 
			"<div style='margin: 2px; padding: 6px; background: #ffffff; border: 1px solid #cccccc; width: auto;'>" .
			// "[{$variable_index}]" .	//	DEBUG
			"{$variable_description}<br />" .
			($choice_manual ? "-- enum: {$variable_enumraw}<br />" : "") .
			(isset($variable_default) ? "<i>default: {$variable_default}</i><br />" : "") .
			"</div>\n";
		if ($choice_select)
		{
			switch ($choice_select_type)
			{
				case "select":
					$text .= 
						"<div style='float: left; width: 120; margin: 2px; border-bottom: 1px solid #cccccc;'><b>{$variable_name}</b></div>\n" .
						"<select name='{$choice_name}' style='margin: 2px;'>";
					foreach ($variable_enum as $enum_value)
					{
						$text .= 
							"<option value='{$enum_value}'" . 
							($enum_value == $variable_value ? " selected" : "") . 
							">{$enum_value}</option>\n";
					}
					// IMPORTANT: This case is not implemented yet.
					if ($choice_select && $choice_manual)
					{
						$text .= 
							"<option value=''" . 
							// ($enum_value == $variable_value ? " selected" : "") . 	// TODO: Implement this case
							">-- manual choice --</option>\n";
					}
					$text .= "</select><br />\n";
				break;
	
				case "radio":
					$text .= 
						"<b>{$variable_name}</b><br />\n" .
						"";
					foreach ($variable_enum as $enum_value)
					{
						$choice_id = formbuild_variable_parameter_id($choice_name);
						$text .= 
							"<input type='radio' name='{$choice_name}' id='{$choice_id}' value='{$enum_value}'" . 
							($enum_value == $variable_value ? " checked" : "") . 
							" style='margin: 2px;' />\n" .
							"<label for='{$choice_id}'>{$enum_value}</label><br />\n";
					}
					// IMPORTANT: This case is not implemented yet.
					if ($choice_select && $choice_manual)
					{
						$choice_id = formbuild_variable_parameter_id($choice_name);
						$text .= 
							"<input type='radio' name='{$choice_name}' id='{$choice_id}' value=''" . 
							// ($enum_value == NULL ? " checked" : "") . 	// TODO: Implement this case
							" style='margin: 2px;' />\n" .
							"<label for='{$choice_id}'><i>manual choice</i></label><br />\n";
					}
				break;
			}
		}
		if ($choice_manual)
		{
			$text .= 
				"<div style='float: left; width: 120; margin: 2px; border-bottom: 1px solid #cccccc;'><b>{$variable_name}</b></div>\n" .
				"<input type='text' name='{$choice_name}' value='{$variable_value}' style='margin: 2px;' />\n" .
				"<br />\n";
		}
		$text .= "</div>\n";
		
		return $text;
	}
	
	function formbuild_section($section, $section_index = 0)
	{
		$section_name = $section['name'];
		$section_description = $section['description'];
		$section_descriptions = $section['descriptions'];
		$section_variables = $section['variables'];

		$text .= "<div style='float: left; clear: none; width: 480; margin: 4px; padding: 4px; border: 2px solid #999999;'>\n";
		
		if ($section_index != 0)
		{
			$section_description_html = nl2br(stripcslashes($section_description));
			$section_parameter_name = formbuild_section_parameter_name($section_name, $section_index, "name");
			$section_parameter_description = formbuild_section_parameter_name($section_name, $section_index, "description");
			// $text .= "section_parameter_name - {$section_parameter_name}<br />";	// DEBUG
			// $text .= "section_parameter_description - {$section_parameter_description}<br />";	// DEBUG
			$text .= 
				"<div style='margin: 4px; border-bottom: 1px dotted #dddddd;'><b>{$section_name}</b></div>" .
				"<div style='margin-left: 12px;'><i>{$section_description_html}</i></div>" .
				"\n";
			$text .= 
				"<input type='hidden' name='{$section_parameter_name}' value='{$section_name}' />" .
				"<input type='hidden' name='{$section_parameter_description}' value='{$section_description}' />" .
				"\n";
		}
		foreach ($section_descriptions as $description_index => $description)
		{
			$description_html = nl2br(stripcslashes($description));
			$text .= 
				"<div style='margin: 4px; padding: 4px; border: 1px solid lightgrey;'>" .
				// "[{$description_index}]" .	//	DEBUG
				$description_html .
				"</div>\n";
			$description_parameter_name = formbuild_description_parameter_name($description_index, $section_name, $section_index);
			// $text .= "description_parameter_name - {$description_parameter_name}<br />";	// DEBUG
			$text .= "<input type='hidden' name='{$description_parameter_name}' value='{$description}' />\n";
		}
		foreach ($section_variables as $variable_index => $variable)
		{
			$text .= formbulid_variable($variable, $variable_index, $section_name, $section_index, FALSE, TRUE, "select");
		}
		$text .= "</div>\n";
		return $text;
	}
	
	function formbuild_config($config)
	{
		$text .= "<form method='post'>";
		$text .= formbuild_section($config);
		foreach ($config['sections'] as $section_index => $section)
		{
			$text .= formbuild_section($section, $section_index);
		}
		$text .=
			"<div style='float: left; clear: both; margin: 20; ' >" .
			"<div style='float: left; width: 120; margin: 2px; border-bottom: 1px solid #cccccc;'><b>Submit</b></div>\n" .
			"<input type='submit' name='submit' id='submit' value='submit' style='margin: 2px;' />\n" .
			"<input type='reset' name='reset' value='reset' style='margin: 2px;' />\n" .
			"</div>" .
			"<br />\n";
		$text .= "</form><br />\n";
		return $text;
	}

// ----------------------------------------------------------------------------

	function inibuild_description(&$array_text, $description)
	{
		$lines = explode('\n', $description);
		// $text .= "lines: " . var_export($lines, true) . "<br />";
		foreach ($lines as $line) $text .= "# {$line}\n";
		return $text;
	}

	function inibuild_variable(&$array_text, $variable)
	{
		$variable_name = $variable['name'];
		$variable_enumraw = $variable['enum-raw'];
		if (isset($variable['default'])) $variable_default = $variable['default'];
		$variable_value = $variable['value'];
		$variable_description = $variable['description'];

		$text .= inibuild_description($array_text, $variable_description);
		$text .= "# {$variable_name}:{$variable_enumraw}\n";
		if (isset($variable_default)) $text .= "# {$variable_name}={$variable_default}\n";
		$text .= "{$variable_name}={$variable_value}\n";
		return $text;
	}

	function inibuild_section(&$array_text, $section, $section_index)
	{
		$section_name = $section['name'];
		$section_description = $section['description'];
		$section_descriptions = $section['descriptions'];
		$section_variables = $section['variables'];

		if ($section_name)
		{
			$description_initext = inibuild_description($array_text, $section_description);
			$section_initext .= 
				"{$description_initext}" .
				"[{$section_name}]\n";
			$text .= $section_initext . "\n";
			$array_text[$section_index] = "{$section_initext}";
		}
		
		foreach ($section_descriptions as $description_index => $description)
		{
			$description_initext = inibuild_description($array_text, $description);
			$text .= "{$description_initext}\n";
			$array_text[$description_index] = $description_initext;
		}

		foreach ($section_variables as $variable_index => $variable)
		{
			$variable_initext = inibuild_variable($array_text, $variable);
			$text .= "{$variable_initext}\n";
			$array_text[$variable_index] = $variable_initext;
		}

		return $text;
	}

	function inibuild_config(&$array_text, $config)
	{
		// $text .= "config: " . var_export($config, true) . "<br />";	// DEBUG
		$text .= inibuild_section($array_text, $config, 0);
		foreach ($config['sections'] as $section_index => $section)
		{
			$text .= inibuild_section($array_text, $section, $section_index);
		}
		return $text;
	}
	
// ----------------------------------------------------------------------------

	$text .= "<b>CTP - Configuration Template Processor</b><br /><br />";
	
	switch ($_SERVER['REQUEST_METHOD'])
	{
		case "GET":
			// file: test-ctp-tpl.ini
			$file_name = "test-ctp-tpl.ini";
			if (file_exists($file_name))
			{
				$config = fileini_parse($file_name);
				if ($config)
				{
					/*
					print "<hr size='1' />";
					print "CONFIG -- "; foreach ($config as $key => $value) { print "{$key}:{$value}, "; } print "<br />\n";	//	DEBUG
					$text .= "<pre>config: " . var_export($config, true) . "</pre>";	// DEBUG
					*/
		
					/*
					print "<hr size='1' />";
					print "section -- index:{$section_index} name:{$config['sections'][$section_index]['name']}<br />";
					$search_variables = $config['sections'][$section_index]['variables'];
					$search_index = search_variable($search_variables, "folder");
					print "search_index: {$search_index}<br />";
					*/
		
					$text .= "<hr size='1' />";
					$text .= formbuild_config($config);
				}
				else
				{
					$text .= "error while parsing the file<br />";
				}
			}
			else
			{
				$text .= "error, file does not exists {$file_name}<br />";
			}
		break;
		
		case "POST":
			$text .= "<pre>";
			// $text .= "_SERVER: " . var_export($_SERVER, true) . "<br />";
			// $text .= "_POST: " . var_export($_POST, true) . "<br />";
			
			$initext .= inibuild_config($array_text, $_POST['ctp_config']);
			$text .= "initext:<br />{$initext}<br />";
			
			ksort($array_text);
			// $text .= "<hr>array_text: " . var_export($array_text, true) . "<br />";	// DEBUG
			$array_text_imploded = implode("\n", $array_text);
			$text .= "<hr>array_text_imploded:\n{$array_text_imploded}<br />";
			
			$text .= "</pre>";
			
		break;
		
	}
	
	print $text;

?>
