<?php

// ----------------------------------------------------------------------------

define ("HTTPAUTH_MODE_ADMIN", "admin");	// when user is the administrator
define ("HTTPAUTH_MODE_ANONYMOUS", "anonymous");	// when user is anonymous, i.e. empty username and password
define ("HTTPAUTH_MODE_UNKNOWN", "unknown");	// when user is unknown, i.e. unrecognized
define ("HTTPAUTH_MODE_UNDEFINED", "undefined");	// when user cancel authentication

define ("HTTPAUTH_REALM", "HTTPAuth Authentication System");

// ----------------------------------------------------------------------------

function httpauth_verify($httpauth_users)
{
	$httpauth = array();
	
	$server_auth_user = $_SERVER['PHP_AUTH_USER'];
	$server_auth_pw = $_SERVER['PHP_AUTH_PW'];
		
	if (isset($server_auth_user))
	{
		// Verify username and password
		$httpauth['mode'] = HTTPAUTH_MODE_UNKNOWN;
		foreach ($httpauth_users as $httpauth_user)
		{
			if ($server_auth_user == $httpauth_user['username'] &&
				$server_auth_pw == $httpauth_user['password'])
			{
				$httpauth['user'] = $httpauth_user['username'];
				$httpauth['mode'] = $httpauth_user['mode'];
				break;
			}
		}
		if ($httpauth['mode'] != HTTPAUTH_MODE_UNKNOWN) 
		{
			$httpauth['authenticated'] = TRUE;
		}
		else
		{
			$httpauth['authenticated'] = FALSE;
		}
	}
	else
	{
		header("WWW-Authenticate: Basic realm=\"" . HTTPAUTH_REALM . "\"");
		header("HTTP/1.0 401 Unauthorized");
		$httpauth['authenticated'] = FALSE;
		$httpauth['mode'] = HTTPAUTH_MODE_UNDEFINED;
	}
	return $httpauth;
}

function httpauth_request()
{
	header("WWW-Authenticate: Basic realm=\"" . HTTPAUTH_REALM . "\"");
	header("HTTP/1.0 401 Unauthorized");
}

// ----------------------------------------------------------------------------

?>
