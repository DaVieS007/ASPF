<?php

	if($URL[1] == "debug" && $URL[2] == "sample")
	{
		$CONTENT = file_get_contents("admin_html/sample.html");
	}
	elseif($URL[1] == "dashboard" && $auth->admin())
	{
		require "pages/dashboard/main.php";
	}
	elseif($URL[1] == "search")
	{
		require "pages/search/main.php";
	}
	elseif($URL[1] == "whitelist" && $auth->admin())
	{
		require "pages/whitelist/main.php";
	}
	elseif($URL[1] == "fast_search")
	{
		require "pages/fast_search/main.php";
	}
	elseif($URL[1] == "blacklist" && $auth->admin())
	{
		require "pages/blacklist/main.php";
	}
	elseif($URL[1] == "limits")
	{
		require "pages/limits/main.php";
	}
	elseif($URL[1] == "clevel")
	{
		require "pages/clevel/main.php";
	}
	else
	{
		$widget->head(12,L("404_NOT_FOUND"));
		$widget->lead(8,L("404_NOT_FOUND_DESC"));
		$CONTENT = $widget->row();
	}

?>