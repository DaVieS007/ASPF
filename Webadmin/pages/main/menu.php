<?php
	$MENU = array();
	if($auth->admin())
	{
		$MENU["dashboard"]["dashboard"]["dashboard"] = L("DASHBOARD");
	}

	$MENU["search"]["search"]["search"] = L("SEARCH");
	if($auth->admin())
	{
		$MENU["search"]["eye"]["whitelist"] = L("WHITELIST");
		$MENU["search"]["eye-slash"]["blacklist"] = L("BLACKLIST");	
	}
	
	$MENU["limits"]["signal"]["limits"] = L("LIMITS");
	$MENU["clevel"]["cogs"]["clevel"] = L("CUSTOM_LEVELS");
	if($auth->SESSION["back_menu"])
	{
		$MENU["backref"]["repeat"][$auth->SESSION["back_menu"]["url"]] = $auth->SESSION["back_menu"]["name"];
	}

//	$AMENU["shift"]["calendar"]["admin/shift"] = L("MANAGE_SHIFT");

?>