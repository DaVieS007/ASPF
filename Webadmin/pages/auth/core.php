<?php

	if($URL[1] == "login")
	{
		if($auth->login($URL[2],$URL[3]))
		{
			$RET = "{!HTML:REFRESH}";
		}
		else
		{
			$RET = "{!LANG:LOGIN_FAILED}";
		}
	}
	elseif($URL[1] == "logout")
	{
		$auth->logout();
		header("Location: /");
		die();
	}
	else
	{
		$RET = file_get_contents("login_html/index.html");
	}
?>