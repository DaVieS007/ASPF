<?php
    $widget->head(12,L("BLACKLIST"));

    /** PROCESS **/
    if(count($_POST))
    {
        $target = $_POST["sender"];
        if(strstr($target,"@"))
        {
            $ID = $DB->query("SELECT ID FROM senders WHERE address = '".$DB->escape($target)."'")->fetch_array()["ID"];
            if($ID)
            {
                $DB->query("UPDATE senders SET type = 'blacklist', expire = '".$DB->escape($_POST["expire"])."' WHERE ID = '".$ID."'");
            }
            else
            {
                $DB->query("INSERT INTO senders (address,type,expire) VALUES ('".$DB->escape($target)."','blacklist','".$DB->escape($_POST["expire"])."');");
            }
        }
        else
        {
            $ID = $DB->query("SELECT ID FROM domains WHERE domain = '".$DB->escape($target)."'")->fetch_array()["ID"];
            if($ID)
            {
                $DB->query("UPDATE domains SET type = 'blacklist', expire = '".$DB->escape($_POST["expire"])."' WHERE ID = '".$ID."'");
            }
            else
            {
                $DB->query("INSERT INTO domains (domain,type,expire) VALUES ('".$DB->escape($target)."','blacklist','".$DB->escape($_POST["expire"])."');");
            }
        }
    }
    /** PROCESS **/

    $sel = array();
    $sel[time() + 3600*24] = L("1DAY");
    $sel[time() + 3600*24*7] = L("1WEEK");
    $sel[time() + 3600*24*31] = L("1MONTH");
    $sel[time() + 3600*24*365] = L("1YEAR");
    $sel[time() + 3600*24*365*10] = L("10YEAR");
    
    $widget->form_input("sender","text",L("SENDER_OR_DOMAIN"),"","");
    $widget->form_select("expire",L("EXPIRE"),"",$sel,0);
    $widget->form(4,"danger",L("ADD_TO_LIST"),L("FORM_SUBMIT"),$url->write($curl));
    $widget->lead(8,L("BLACKLIST_HELP"));
    $CONTENT .= $widget->row();

    if($_GET["remove"])
    {
        $DB->query("DELETE FROM senders WHERE ID = '".$DB->escape($_GET["remove"])."'");
        $url->go($URL);
    }
    else if($_GET["remove_domain"])
    {
        $DB->query("DELETE FROM domains WHERE ID = '".$DB->escape($_GET["remove_domain"])."'");
        $url->go($URL);
    }

	/** LIST_SENDERS **/
	$table = array();
	$table["th"] = array(L("SENDER"),L("UNTIL"),"");
	$res = $DB->query("SELECT * FROM `senders` WHERE type = 'blacklist'");
	while($row = $res->fetch_array())
	{
		$sender = $row["address"];

		$table["td"][] = array(htmlspecialchars($sender),date($config["date_format"],$row["expire"]),$widget->button("danger",L("REMOVE"),$url->write($URL)."?remove=".$row["ID"]));
	}

	$widget->table(6,L("BLACKLIST_SENDERS"),$table["th"],$table["td"],"dt_blsenders","1:desc");
	/** LIST_SENDERS **/

    /** LIST_DOMAINS **/
	$table = array();
	$table["th"] = array(L("SENDER"),L("UNTIL"),"");
	$res = $DB->query("SELECT * FROM `domains` WHERE type = 'blacklist'");
	while($row = $res->fetch_array())
	{
		$sender = $row["domain"];

		$table["td"][] = array(htmlspecialchars($sender),date($config["date_format"],$row["expire"]),$widget->button("danger",L("REMOVE"),$url->write($URL)."?remove_domain=".$row["ID"]));
		
	}

	$widget->table(6,L("BLACKLIST_DOMAINS"),$table["th"],$table["td"],"dt_bldomains","1:desc");
	/** LIST_DOMAINS **/


    $CONTENT .= $widget->row();
?>