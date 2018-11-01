<?php

	require "menu.php";

	$MAIN = file_get_contents("admin_html/main.html");
	$MENU_ENTRY = "";

	/** REDIRECT_TO_DEFAULT **/
	if($URL[1] == "")
	{
		if($auth->reseller())
		{
			$nurl = $URL;
			$nurl[1] = "search";
			$url->go($nurl);	
		}
		else
		{
			$nurl = $URL;
			$nurl[1] = "dashboard";
			$url->go($nurl);	
		}
	}
	/** REDIRECT_TO_DEFAULT **/

	require "wrapper.php";

    $menu_sep = file_get_contents("common_html/menu_sep.html");

	/** GET CUR_MENU **/
	$turl = $URL;
	unset($turl[0]);
	$turl = implode("/",$turl);
	/** GET CUR_MENU **/

	/** PROCESS_MENU **/
    $menu_item = file_get_contents("admin_html/menu_item.html");
    $bar_item = file_get_contents("admin_html/bar_item.html");

    $RET_MENU = "";
    $RET_AMENU = "";
    $RET_UMENU = "";
    $RET_GMENU = "";
    while(list($key,$arr) = each($MENU))
    {
    	if(!is_array($arr) && $arr == "sep")
    	{
    		$RET_MENU .= $menu_sep;
    	}
    	else
    	{
	    	while(list($icon,$arr2) = each($arr))
	    	{
		    	while(list($link,$name) = each($arr2))
		    	{
		    		$temp = $menu_item;
		    		$temp = str_replace("[ICON]",$icon,$temp);
		    		$temp = str_replace("[TITLE]",$name,$temp);
		    		$temp = str_replace("[LINK]",$link,$temp);
		    		if(strstr($turl,$link))
		    		{
		    			$MENU_ENTRY = $name;
			    		$temp = str_replace("[ACTIVE]","active",$temp);
		    		}
		    		else
		    		{
			    		$temp = str_replace("[ACTIVE]","",$temp);
			    	}
			    	$RET_MENU .= $temp;
			    }
	    	}
    	}
    }   


    $CONTENT = str_replace("[MENU_ENTRY]",$MENU_ENTRY,$CONTENT);


    $MAIN = str_replace("[MENU_DATA]",$RET_MENU,$MAIN);
    $MAIN = str_replace("[MAIN]",$CONTENT,$MAIN);
    $RET = $MAIN;
	/** PROCESS_MENU **/


?>