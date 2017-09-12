<?php

    /** LIMIT **/
    function limit(&$DB,&$arr,&$msg,&$srv_info)
    {
        global $config;

        $sender = $arr["real_sender"];
        $recipient = $arr["recipient"];
        if(strstr($recipient,","))
        {
            $tmp = explode(",",$recipient);
            while(list($k,$v) = each($tmp))
            {
                if($v)
                {
                    $narr = $arr;
                    $narr["recipient"] = trim($v);
                    $_ret = limit($DB,$narr,$msg,$srv_info);    
                    if($_ret == "reject")
                    {
                        return "reject";
                    }    
                }
            }
            return $_ret;
        }

        $client_address = $arr["client_address"];
        $client_name = gethostbyaddr($arr["client_address"]);
        $reverse_client_name = $client_name;
        $sender_domain = explode("@",$sender)[1];
        
        $srv_info["client_ip"] = $client_address;
        $srv_info["client_name"] = $client_name;

        /** TRY BLACKLIST **/
        $domain_rule = $DB->query("SELECT type,expire FROM domains WHERE domain = '".$DB->escape($sender_domain)."' AND expire > '".time()."';")->fetch_array();
        if($domain_rule["type"] == "blacklist")
        {
            $msg = "ASPF: Your message is rejected due sender domain is on blacklist until: ".sdate($domain_rule["expire"]);
            add_transaction($DB,$sender,$recipient,"blacklist",$msg,"",$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);            
            return "reject";
        }
        /** TRY BLACKLIST **/

        /** GET_LIMIT **/
        $limit = $DB->query("SELECT `limit` FROM mail_limit WHERE address = '".$DB->escape($sender)."'")->fetch_array()["limit"];
        if(!$limit)
        {
            $limit = $DB->query("SELECT `limit` FROM mail_limit WHERE domain = '".$DB->escape($sender_domain)."'")->fetch_array()["limit"];
            if($limit)            
            {
                mlog("Validate","NOTICE","Domain-Based Custom-Limit: ".$limit);                                                
            }
        }
        else
        {
            mlog("Validate","NOTICE","User-Based Custom-Limit: ".$limit);                                
        }

        if(!$limit)
        {
            $limit = $config["ANTISPAM"]["limit_mails_per_user"];
        }
        /** GET_LIMIT **/


        $reject_on_limit = $config["ANTISPAM"]["enable_limit_reject"];
        if($limit)
        {
            $ts = time() - 60*5; // 5 Minute
            $count = $DB->query("SELECT COUNT(ID) AS IDX FROM transactions WHERE sender = '".$DB->escape($sender)."' AND tstamp > '".$ts."'")->fetch_array()["IDX"];
            if($count > $limit)
            {
                if($reject_on_limit)
                {
                    $msg = "ASPF: Your message rejected due to exceeded ".$limit." / 5 Minutes limit, try again later.";
                    add_transaction($DB,$sender,$recipient,"limit",$msg,"",$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
                    return "reject";
                }
                else
                {
                    add_transaction($DB,$sender,$recipient,"limit",$msg,"",$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
                    return "notify";
                }
            }
        }

        /** GRAYLIST_OPERATION **/
        if($config["GRAYLIST"]["gray_learn_recipient_domain"])
        {
            $recipient_domain = explode("@",$recipient)[1];
            if(learn_domain($DB,$recipient_domain))
            {
                mlog("Limit","NOTICE","Added Recipient Domain to whitelist: ".$recipient_domain);
            }
        }
        else if($config["GRAYLIST"]["gray_learn_recipient_mail"])
        {
            if(learn_mail($DB,$recipient))
            {
                mlog("Limit","NOTICE","Added Recipient Address to whitelist: ".$recipient);
            }
        }
        /** GRAYLIST_OPERATION **/

        add_transaction($DB,$sender,$recipient,"sent","","",$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
        return "sent";
    }
    /** LIMIT **/
?>