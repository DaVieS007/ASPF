<?php

	if($URL[1] == "debug" && $URL[2] == "sample")
	{
		$CONTENT = file_get_contents("admin_html/sample.html");
	}
	elseif($URL[1] == "dashboard")
	{
		require "pages/dashboard/main.php";
	}
	elseif($URL[1] == "search")
	{
		require "pages/search/main.php";
	}
	elseif($URL[1] == "whitelist")
	{
		require "pages/whitelist/main.php";
	}
	elseif($URL[1] == "blacklist")
	{
		require "pages/blacklist/main.php";
	}
	else
	{
		$widget->head(12,L("404_NOT_FOUND"));
		$widget->lead(8,L("404_NOT_FOUND_DESC"));
		$CONTENT = $widget->row();
	}

?>