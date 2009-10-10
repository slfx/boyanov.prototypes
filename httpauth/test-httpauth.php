<?php

require_once "httpauth-inc.php";

// ----------------------------------------------------------------------------

	$httpauth_users = array (
		array ( 'username' => 'admin', 'password' => 'admin', 'mode' => 'admin' ),
		array ( 'username' => '', 'password' => '', 'mode' => 'anonymous' ),
		array ( 'username' => 'test', 'password' => 'test', 'mode' => 'test' ),
		);
	// print "httpauth_users:<pre>" . var_export($httpauth_users, true) . "</pre><br />";	// DEBUG

// ----------------------------------------------------------------------------

	$server_server_name = $_SERVER['SERVER_NAME'];
	$server_script_name = $_SERVER['SCRIPT_NAME'];
	
	/*
	$server_auth_user = $_SERVER['PHP_AUTH_USER'];
	$server_auth_pw = $_SERVER['PHP_AUTH_PW'];
	print "server_auth_user '{$server_auth_user}' server_auth_pw '{$server_auth_pw}'";
	*/
	
	$httpauth = httpauth_verify($httpauth_users);
	// print "httpauth:<pre>" . var_export($httpauth, true) . "</pre><br />";	// DEBUG
	
	if (isset($_REQUEST['signout'])) httpauth_request();

// ----------------------------------------------------------------------------

	print "<br />";
	
	if ($httpauth['authenticated'])
	{
		print "status: Authenticated, username and password recognized.<br />";
		switch ($httpauth['mode'])
		{
			case HTTPAUTH_MODE_ADMIN:
				print "mode: Admin, the user is an administrator.<br />";
			break;
	
			case HTTPAUTH_MODE_ANONYMOUS:
				print "mode: Anonymous, empty username and password were provided.<br />";
			break;
	
			default:
				print "mode-custom: {$httpauth['mode']}<br />";
			break;
		}
		// Link for sign-out
		$link_signout = "http://{$server_server_name}{$server_script_name}?signout";
		print "[<a href='$link_signout'>Sign-out</a>]<br />";
	}
	else
	{
		httpauth_request();	// ==>> Request to signin again.
		print "status: Not-authenticated, username and password not recognized.<br />";
		switch ($httpauth['mode'])
		{
			case HTTPAUTH_MODE_UNKNOWN:
				print "mode: Unknown, username and password were not recognized.<br />";
			break;
	
			case HTTPAUTH_MODE_UNDEFINED:
				print "mode: Undefined, authentication canceled.<br />";
			break;
	
			default:
				print "mode-custom: {$httpauth['mode']}<br />";
			break;
		}
		// Debug & Test
		$link_signin_test = "http://test:test@{$server_server_name}{$server_script_name}";	// DEBUG
		print "Test: [<a href='$link_signin_test'>Sign-in</a>]<br />";
	}
	
?>
