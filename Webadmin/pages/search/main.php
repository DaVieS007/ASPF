<?php
    if($_GET["search"])
    {
        $url->go(array($URL[0],$URL[1],$_GET["search"]));
    }

    $widget->head(12,L("SEARCH"));
    $widget->searchbar(12,L("SEARCH"),$URL[2]);

    /** MAIL_STATE **/
    function mail_state($type,$expire)
    {
        global $widget;
        global $config;

        if($type == "blacklist")
        {
            return $widget->bold(L("BLACKLISTED")."<br />".$widget->badge(L("UNTIL").": ".date($config["date_format"],$expire),"danger"));
        }
        else if($type == "whitelist")
        {
            return $widget->bold(L("WHITELISTED")."<br />".$widget->badge(L("UNTIL").": ".date($config["date_format"],$expire),"success"));
        }
        else if($type == "cache")
        {
            return $widget->bold(L("CACHED")."<br />".$widget->badge(L("UNTIL").": ".date($config["date_format"],$expire),"primary"));
        }
        else
        {
            return $widget->bold(L("UNKNOWN"));
        }

    }
    /** MAIL_STATE **/

    if($URL[3])
    {
        $data = $DB->query("SELECT * FROM transactions WHERE ID = '".$DB->escape($URL[3])."'")->fetch_array(MYSQLI_ASSOC);
        if($data["ID"])
        {

            $sender = $data["sender"];
            $sender_domain = explode("@",$sender)[1];
    
            $recipient = $data["recipient"];
            $recipient_domain = explode("@",$recipient)[1];

            $access = true;

            if($auth->reseller())
            {
                if(!isset($allowed_domains[explode("@",$sender)[1]]) && !isset($allowed_domains[explode("@",$recipient)[1]]))
                {
                    unset($URL[3]);
                    $url->go($URL);
                }    
            }
    

            /** PROCESS **/
            $exts = time() + 365*24*3600; // 1 Year
            if($_GET["action"] == "blacklist")
            {
                $ID = $DB->query("SELECT * FROM senders WHERE address = '".$DB->escape($sender)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE senders SET type = 'blacklist', expire = '".$exts."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO senders (address,type,expire) VALUES ('".$DB->escape($sender)."','blacklist','".$exts."');");
                }
            }
            else if($_GET["action"] == "blacklist_domain")
            {
                $ID = $DB->query("SELECT * FROM domains WHERE domain = '".$DB->escape($sender_domain)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE domains SET type = 'blacklist', expire = '".$exts."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO domains (domain,type,expire) VALUES ('".$DB->escape($sender_domain)."','blacklist','".$exts."');");
                }
                
            }
            else if($_GET["action"] == "whitelist")
            {
                $ID = $DB->query("SELECT * FROM senders WHERE address = '".$DB->escape($sender)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE senders SET type = 'whitelist', expire = '".$exts."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO senders (address,type,expire) VALUES ('".$DB->escape($sender)."','whitelist','".$exts."');");
                }
            }
            else if($_GET["action"] == "whitelist_domain")
            {
                $ID = $DB->query("SELECT * FROM domains WHERE domain = '".$DB->escape($sender_domain)."'")->fetch_array()["ID"];
                if($ID)
                {
                    $DB->query("UPDATE domains SET type = 'whitelist', expire = '".$exts."' WHERE ID = '".$ID."'");
                }
                else
                {
                    $DB->query("INSERT INTO domains (domain,type,expire) VALUES ('".$DB->escape($sender_domain)."','whitelist','".$exts."');");
                }
                
            }
            else if($_GET["action"] == "default")
            {
                $DB->query("DELETE FROM senders WHERE address = '".$DB->escape($sender)."'");
            }
            else if($_GET["action"] == "default_domain")
            {
                $DB->query("DELETE FROM domains WHERE domain = '".$DB->escape($sender_domain)."'");
            }

            if($_GET["action"])
            {
                $url->go($URL);
            }
            /** PROCESS **/

            $widget->head(12,"ASPF-".$data["ID"]);
            $table = array();
            $table["th"] = array("","");
            while(list($k,$v) = each($data))
            {
                if($k == "tstamp")
                {
                    $v = date($config["date_format"],$v);
                }

                $table["td"][] = array($widget->bold(ucfirst($k)),htmlspecialchars($v));
            }
            $widget->table(7,"",$table["th"],$table["td"]);


            $sender = $data["sender"];
            $sender_domain = explode("@",$sender)[1];
    
            $recipient = $data["recipient"];
            $recipient_domain = explode("@",$recipient)[1];
    
            $sender_state = $DB->query("SELECT ID,expire,type FROM senders WHERE address = '".$DB->escape($sender)."'")->fetch_array();
            $recipient_state = $DB->query("SELECT ID,expire,type FROM senders WHERE address = '".$DB->escape($recipient)."'")->fetch_array();
    
            $sender_domain_state = $DB->query("SELECT ID,expire,type FROM domains WHERE domain = '".$DB->escape($sender_domain)."'")->fetch_array();
            $recipient_domain_state = $DB->query("SELECT ID,expire,type FROM domains WHERE domain = '".$DB->escape($recipient_domain)."'")->fetch_array();

            /** STATE **/
            $table = array();
            $table["th"] = array("","","","");
    
            $table["td"][] = array(
                $widget->bold(L("SENDER")),
                htmlspecialchars($sender),
                mail_state($sender_state["type"],$sender_state["expire"])
            );    
    
            $table["td"][] = array(
                $widget->bold(L("RECIPIENT")),
                htmlspecialchars($recipient),
                mail_state($recipient_state["type"],$recipient_state["expire"])
            );    
    
            $table["td"][] = array(
                $widget->bold(L("SENDER_DOMAIN")),
                $sender_domain,
                mail_state($sender_domain_state["type"],$sender_domain_state["expire"])
            );    
    
            $table["td"][] = array(
                $widget->bold(L("RECIPIENT_DOMAIN")),
                $recipient_domain,
                mail_state($recipient_domain_state["type"],$recipient_domain_state["expire"])
            );    
            
            $widget->table(5,"",$table["th"],$table["td"]);
            /** STATE **/
    
            /** FAST_ACTION **/
            $table = array();
            $table["th"] = array("","");
            $table["td"][] = array(
                $widget->button("success",L("ADD_SENDER_TO_WHITELIST"),$url->write($URL)."?action=whitelist"),
                $widget->button("success",L("ADD_SENDER_DOMAIN_TO_WHITELIST"),$url->write($URL)."?action=whitelist_domain")
            );
    
            $table["td"][] = array(
                $widget->button("danger",L("ADD_SENDER_TO_BLACKLIST"),$url->write($URL)."?action=blacklist"),
                $widget->button("danger",L("ADD_SENDER_DOMAIN_TO_BLACKLIST"),$url->write($URL)."?action=blacklist_domain")
            );

            $table["td"][] = array(
                $widget->button("primary",L("REMOVE_SENDER"),$url->write($URL)."?action=default"),
                $widget->button("primary",L("REMOVE_SENDER_DOMAIN"),$url->write($URL)."?action=default_domain")
            );
            $widget->table(5,"",$table["th"],$table["td"]);
            /** FAST_ACTION **/

        }
    }

    if($URL[2])
    {
        $widget->head(12,L("RESULTS").": ".htmlspecialchars($URL[2]));

        /** RESULTS **/
        $table = array();
        $table["th"] = array(L("SENDER"),L("RECIPIENT"),L("SMTP_NAME"),L("SENDER_NAME"),L("DATE"),"");

        $access = true;
        if($auth->reseller())
        {
            if(!isset($allowed_domains[$URL[2]]) && !isset($allowed_domains[explode("@",$URL[2])[1]]))
            {
                $access = false;
            }    
        }

        if($access)
        {
            $res = $DB->query("SELECT ID,smtp_ip,smtp_name,sender_ip,sender_name,sender,recipient,action, tstamp FROM `transactions` WHERE tstamp > '".$ts."' AND (sender LIKE '%".$DB->escape($URL[2])."%' OR recipient LIKE '%".$DB->escape($URL[2])."%' ) ORDER BY tstamp DESC LIMIT 0,1000");
            while($row = $res->fetch_array())
            {
                $sender = mailb($row["sender"],40);
                if($row["action"] == "blacklist")
                {
                    $sender .= "<br />".$widget->badge(L("OUTGOING"),"danger")."  ".$widget->badge(L("BLACKLISTED"),"danger");
                }
                else if($row["action"] == "limit")
                {
                    $sender .= "<br />".$widget->badge(L("OUTGOING"),"danger")."  ".$widget->badge(L("LIMITED"),"primary");
                }
                else if($row["action"] == "reject")
                {
                    $sender .= "<br />".$widget->badge(L("INCOMING"),"primary")."  ".$widget->badge(L("REJECTED"),"warning");
                }
                else if($row["action"] == "dunno")
                {
                    $sender .= "<br />".$widget->badge(L("INCOMING"),"primary")."  ".$widget->badge(L("DUNNO"),"info");
                }
                else if($row["action"] == "sent")
                {
                    $sender .= "<br />".$widget->badge(L("OUTGOING"),"danger")."  ".$widget->badge(L("ACCEPTED"),"success");
                }
                else if($row["action"] == "accept")
                {
                    $sender .= "<br />".$widget->badge(L("INCOMING"),"primary")."  ".$widget->badge(L("ACCEPTED"),"success");
                }
    
                $nurl = $URL;
                $nurl[1] = "search";
                $nurl[2] = urlencode($row["sender"]);
                $nurl[3] = $row["ID"];
                $table["td"][] = array(
                    $sender,
                    mailb($row["recipient"],40),
                    $row["smtp_ip"]."<br />".$widget->badge($row["smtp_name"],"primary"),
                    $row["sender_ip"]."<br />".$widget->badge($row["sender_name"],"primary"),
                    date($config["date_format"],$row["tstamp"]),
                $widget->button("danger",L("INVESTIGATE"),
                $url->write($nurl)));
            }
        }

	    $widget->table(12,"",$table["th"],$table["td"],"dt_results","4:desc");
	    /** RESULTS **/        
    }

    $CONTENT .= $widget->row();
?>