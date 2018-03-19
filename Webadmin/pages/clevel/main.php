<?php
    $widget->head(12,L("CUSTOM_LEVELS"));

    /** PROCESS **/
    if(count($_POST))
    {
        $target = $_POST["sender"];

        $access = true;
        if($access)
        {
            if(strstr($target,"@"))
            {
                $ID = $DB->query("SELECT ID FROM custom_level WHERE address = '".$DB->escape($target)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE custom_level SET level = '".$DB->escape($_POST["level"])."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO custom_level (address,level) VALUES ('".$DB->escape($target)."','".$DB->escape($_POST["level"])."');");
                }
            }
            else
            {
                $ID = $DB->query("SELECT ID FROM custom_level WHERE domain = '".$DB->escape($target)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE custom_level SET level = '".$DB->escape($_POST["level"])."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO custom_level (domain,level) VALUES ('".$DB->escape($target)."','".$DB->escape($_POST["level"])."');");
                }
            }
        }
    }
    /** PROCESS **/

    $mitigation_level = array();
    $mitigation_level["0"] = L("MITIGATION_0");
    $mitigation_level["1"] = L("MITIGATION_1");
    $mitigation_level["2"] = L("MITIGATION_2");
    $mitigation_level["3"] = L("MITIGATION_3");
    $mitigation_level["4"] = L("MITIGATION_4");
    $mitigation_level["5"] = L("MITIGATION_5");

    $widget->form_input("sender","text",L("SENDER_OR_DOMAIN"),"","");
    $widget->form_select("level",L("MITIGATION_LEVEL"),"",$mitigation_level,"");
    $widget->form(4,"success",L("ADD_TO_LIST"),L("FORM_SUBMIT"),$url->write($curl));
    $widget->lead(8,L("CLEVEL_HELP"));
    $CONTENT .= $widget->row();

    if($_GET["remove"])
    {
        $DB->query("DELETE FROM custom_level WHERE ID = '".$DB->escape($_GET["remove"])."'");
        $url->go($URL);
    }

	/** LIST_SENDERS **/
	$table = array();
	$table["th"] = array(L("MAIL"),L("CLEVEL"),"");
	$res = $DB->query("SELECT * FROM `custom_level` WHERE address != ''");
	while($row = $res->fetch_array())
	{
		$sender = $row["address"];
		$table["td"][] = array(htmlspecialchars($sender),"LV-".$row["level"],$widget->button("danger",L("REMOVE"),$url->write($URL)."?remove=".$row["ID"]));
	}

	$widget->table(6,L("CLEVEL_MAILS"),$table["th"],$table["td"],"dt_wlsenders","1:desc");
	/** LIST_SENDERS **/

    /** LIST_DOMAINS **/
	$table = array();
	$table["th"] = array(L("DOMAIN"),L("CLEVEL"),"");
	$res = $DB->query("SELECT * FROM `custom_level` WHERE domain != ''");
	while($row = $res->fetch_array())
	{
		$sender = $row["domain"];
		$table["td"][] = array(htmlspecialchars($sender),"LV-".$row["level"],$widget->button("danger",L("REMOVE"),$url->write($URL)."?remove=".$row["ID"]));
	}

	$widget->table(6,L("CLEVEL_DOMAINS"),$table["th"],$table["td"],"dt_wldomains","1:desc");
	/** LIST_DOMAINS **/


    $CONTENT .= $widget->row();
?>