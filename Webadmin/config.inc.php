<?php
	error_reporting(E_ERROR);
        date_default_timezone_set('Europe/Budapest');

	$config = array();
	$config["langs"] = array("en", "hu");
	$config["default_language"] = "en";

	/* EDIT THIS LINE **/
	$config["admin_password"] = "console";
	/* EDIT THIS LINE **/

	$config["force_ssl"] = true;
	
	$config["mysql_host"] = "localhost";
	$config["mysql_user"] = "aspf";
	$config["mysql_db"] = "mvcp_aspf";
	$config["mysql_passwd"] = "console";

	$config["session_timeout"] = 3600; //seconds

	$config["date_format"] = "Y/m/d H:i:s";
	$config["date_format_min"] = "Y/m/d";
	$config["show_latest"] = 72; //HOURS
?>