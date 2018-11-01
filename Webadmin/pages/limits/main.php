<?php
    $widget->head(12,L("LIMITS"));

    /** PROCESS **/
    if(count($_POST))
    {
        $target = $_POST["sender"];
        $access = true;

        if($auth->reseller())
        {
            if(!isset($allowed_domains[explode("@",$target)[1]]) && !isset($allowed_domains[$target]))
            {
                $access = false;
                $widget->form_note("danger",L("ACCESS_DENIED"));
            }    
        }

        if($access)
        {
            if(strstr($target,"@"))
            {
                $ID = $DB->query("SELECT ID FROM mail_limit WHERE address = '".$DB->escape($target)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE mail_limit SET `limit` = '".$DB->escape($_POST["limit"])."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO mail_limit (address,`limit`) VALUES ('".$DB->escape($target)."','".$DB->escape($_POST["limit"])."');");
                }
            }
            else
            {
                $ID = $DB->query("SELECT ID FROM mail_limit WHERE domain = '".$DB->escape($target)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE mail_limit SET `limit` = '".$DB->escape($_POST["limit"])."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO mail_limit (domain,`limit`) VALUES ('".$DB->escape($target)."','".$DB->escape($_POST["limit"])."');");
                }
            }
        }
    }
    /** PROCESS **/
    
    if($_GET["edit"] > 0)
    {
        $tmp = $DB->query("SELECT * FROM mail_limit WHERE ID = '".$DB->escape($_GET["edit"])."'")->fetch_array();
        if($tmp["domain"])
        {
            $sender = $tmp["domain"];
        }
        else
        {
            $sender = $tmp["address"];
        }

        $limit = $tmp["limit"];
    }

    $widget->form_input("sender","text",L("SENDER_OR_DOMAIN"),"",$sender);
    $widget->form_input("limit","text",L("SENDING_LIMIT"),"",$limit);
    $widget->form(4,"primary",L("ADD_TO_LIST"),L("FORM_SUBMIT"),$url->write($curl));
    $widget->lead(8,L("BLACKLIST_HELP"));
    $CONTENT .= $widget->row();

    if($_GET["remove"])
    {
        $DB->query("DELETE FROM mail_limit WHERE ID = '".$DB->escape($_GET["remove"])."'");
        $url->go($URL);
    }

	/** LIST_LIMITS_MAILS **/
	$table = array();
	$table["th"] = array(L("SENDER"),L("LIMIT"),"");
	$res = $DB->query("SELECT * FROM `mail_limit` WHERE address != ''");
	while($row = $res->fetch_array())
	{
		$sender = $row["address"];

        if($auth->reseller())
        {
            if(!isset($allowed_domains[explode("@",$sender)[1]]))
            {
                continue;
            }    
        }
		$table["td"][] = array(htmlspecialchars($sender),$row["limit"],$widget->button("danger",L("REMOVE"),$url->write($URL)."?remove=".$row["ID"]).$widget->button("warning",L("EDIT"),$url->write($URL)."?edit=".$row["ID"]));
	}

	$widget->table(6,L("MAIL_LIMIT"),$table["th"],$table["td"],"dt_lsenders","1:desc");
	/** LIST_LIMITS_MAILS **/

    /** LIST_DOMAINS **/
	$table = array();
	$table["th"] = array(L("SENDER_DOMAIN"),L("LIMIT"),"");
	$res = $DB->query("SELECT * FROM `mail_limit` WHERE domain != ''");
	while($row = $res->fetch_array())
	{
		$sender = $row["domain"];

        if($auth->reseller())
        {
            if(!isset($allowed_domains[$sender]))
            {
                continue;
            }    
        }


		$table["td"][] = array(htmlspecialchars($sender),$row["limit"],$widget->button("danger",L("REMOVE"),$url->write($URL)."?remove=".$row["ID"]).$widget->button("warning",L("EDIT"),$url->write($URL)."?edit=".$row["ID"]));		
	}

	$widget->table(6,L("DOMAIN_LIMIT"),$table["th"],$table["td"],"dt_ldomains","1:desc");
	/** LIST_DOMAINS **/


    $CONTENT .= $widget->row();
?>