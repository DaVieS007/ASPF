<?php
	$MENU = array();
	$MENU["dashboard"]["dashboard"]["dashboard"] = L("DASHBOARD");
	$MENU["search"]["search"]["search"] = L("SEARCH");
	$MENU["search"]["eye"]["whitelist"] = L("WHITELIST");
	$MENU["search"]["eye-slash"]["blacklist"] = L("BLACKLIST");
	if($auth->SESSION["back_menu"])
	{
		$MENU["backref"]["repeat"][$auth->SESSION["back_menu"]["url"]] = $auth->SESSION["back_menu"]["name"];
	}

//	$AMENU["shift"]["calendar"]["admin/shift"] = L("MANAGE_SHIFT");

?>