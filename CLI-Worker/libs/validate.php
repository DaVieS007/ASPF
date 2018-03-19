<?php
    /** VALIDATE **/
    function validate(&$DB,&$arr,&$msg,&$msg2,&$srv_info)
    {
        global $config;

        $sender = $arr["sender"];
        $real_sender = $arr["real_sender"];
        $recipient = $arr["recipient"];
        $client_address = $arr["client_address"];
        $helo_name = $arr["helo_name"];
        $sender_domain = explode("@",$sender)[1];
        $recipient_domain = explode("@",$recipient)[1];
        
        $client_name = gethostbyaddr($arr["client_address"]);
        $reverse_client_name = gethostbyaddr(gethostbyname($client_name));

        $client_domain = $helo_name;
        
        $srv_info["client_ip"] = $client_address;
        $srv_info["client_name"] = $client_name;
        
        /** DETECT INCOMING SPAM **/
        if($config["SPAM_DETECT"]["spam_mitigation_level"] >= 1)
        {
            /** TRY BLACK/WHITELIST **/
            $domain_rule = $DB->query("SELECT type,expire FROM domains WHERE domain = '".$DB->escape($sender_domain)."' AND expire > '".time()."';")->fetch_array();
            if($domain_rule["type"] == "whitelist" || $domain_rule["type"] == "auto-whitelist")
            {
                $msg2 = "Accept-Domain-On-Whitelist (".sdate($domain_rule["expire"]).")";
                return "accept";
            }
            else if($domain_rule["type"] == "blacklist")
            {
                $msg = "ASPF: Your message is rejected due sender domain is on blacklist until: ".sdate($domain_rule["expire"]);
                return "reject";
            }
            /** TRY BLACK/WHITELIST **/

            /** TRY BLACK/WHITELIST **/
            $sender_rule = $DB->query("SELECT type,expire FROM senders WHERE address = '".$DB->escape($sender)."' AND expire > '".time()."';")->fetch_array();
            if($sender_rule["type"] == "whitelist" || $sender_rule["type"] == "auto-whitelist")
            {
                $msg2 = "Accept-Sender-On-Whitelist Until: (".sdate($sender_rule["expire"]).")";
                return "accept";
            }
            else if($sender_rule["type"] == "cache")
            {
                $msg2 = "Accept-Sender-On-Cache Until: (".sdate($sender_rule["expire"]).")";
                return "accept";                
            }
            else if($sender_rule["type"] == "blacklist")
            {
                $msg = "ASPF: Your message is rejected due sender address is on blacklist until: ".sdate($sender_rule["expire"]);
                return "reject";
            }
            /** TRY BLACK/WHITELIST **/

            $custom_level = $DB->query("SELECT level FROM custom_level WHERE address = '".$DB->escape($recipient)."'")->fetch_array()["level"];
            if(!$custom_level)
            {
                $custom_level = $DB->query("SELECT level FROM custom_level WHERE domain = '".$DB->escape($recipient_domain)."'")->fetch_array()["level"];                
                if($custom_level)
                {
                    mlog("Validate","NOTICE","Domain-Based Custom-Level: ".$custom_level);                    
                }
            }
            else
            {
                mlog("Validate","NOTICE","User-Based Custom-Level: ".$custom_level);
            }
            
            if(!$custom_level)
            {
                $custom_level = $config["SPAM_DETECT"]["spam_mitigation_level"];
            }

            mlog("Validate","NOTICE","Accept JOB: ".$sender." -> ".$recipient);                
            
            /** ROUND - 1 **/
            if($custom_level >= 1)
            {
                mlog("Validate","NOTICE","Round-1 | Checking MXes on domain: ".$sender_domain);                
                $mxes = dns_get_record ($sender_domain, DNS_MX);
                if(!count($mxes))
                {
                    $msg = "ASPF: Your message is rejected due no MX record found on your sender domain (".$sender_domain."), try later.";
                    $msg2 = "Marked-As-SPAM due no MX record found on sender domain (".$sender_domain.")";
                    return "dunno";
                }
            }
            /** ROUND - 1 **/

            /** ROUND - 2 **/
            if($custom_level >= 2)
            {
                mlog("Validate","NOTICE","Round-2 | Checking Reverse: ".$reverse_client_name." if eq: ".$client_name." but not: ".$client_address);
                if($reverse_client_name != $client_name || $client_name == $client_address)
                {
                    $msg = "ASPF: Your message is rejected due your hostname is not equal with your reverse domain or with HELO/EHLO name, try later.";
                    $msg2 = "Marked-As-SPAM due hostname is not equal with reverse domain or with HELO/EHLO name";
                    return "dunno";
                }
            }
            /** ROUND - 2 **/
            
            /** ROUND - 3 **/
            if($custom_level >= 3)
            {
                mlog("Validate","NOTICE","Round-3 | Probing MX: ".$sender_domain);                
                $found_mx = probe_mx($sender,$mxes);

                if(!$found_mx)
                {
                    $msg = "ASPF: Your message is rejected due no valid SMTP Server found on domain: (".$sender_domain."), try later.";
                    $msg2 = "Marked-As-SPAM due no valid SMTP Server found on domain: (".$sender_domain.")";
                    return "dunno";                    
                }
            }
            /** ROUND - 3 **/

            $_found_mx = "INET".substr($found_mx,0,1)."/".substr($found_mx,1);
            /** ROUND - 4 **/
            if($custom_level >= 4)
            {
                mlog("Validate","NOTICE","Round-4 | RBL Checking");                

                /** RBL_CHECK **/
                $rbl = rbl_check($srv_info["client_ip"]);
                if($rbl)
                {
                    $msg = "ASPF: Your message is rejected due sender host is on list ".$rbl;
                    $msg2 = "Marked-As-SPAM due sender host is found on list ".$rbl;
                    return "dunno";                                                                                
                }
            }
            /** ROUND - 4 **/

            /** ROUND - 5 **/
            if($custom_level >= 5)
            {
                mlog("Validate","NOTICE","Round-5 | Checking sender origin");                
                if(strstr(strtolower($sender),"noreply") || strstr(strtolower($sender),"no-reply") || strstr(strtolower($sender),"hirlevel") || strstr(strtolower($sender),"hirlevel"))
                {
                    $msg = "ASPF: Your message is rejected due our system is only accept messages from real ppl.";
                    $msg2 = "Marked-As-SPAM due our system is only accept messages from real ppl";
                    return "dunno";                    
                }
            }
            /** ROUND - 5 **/

            /** ROUND - 6 **/
            if($custom_level >= 6)
            {
                mlog("Validate","NOTICE","Round-6 | OS-Probe Started on Sender/MX: (".$srv_info["client_ip"]."/".$_found_mx.")");
                $r1 = os_probe($srv_info["client_ip"]);
                $r2 = os_probe(substr($found_mx,1));

                $MIN_SCORE = 40;
                if(($r1 + $r2) < 40)
                {
                    $msg = "ASPF: Your message is rejected due seems SPAM";
                    $msg2 = "Marked-As-SPAM due this message seems SPAM";
                    return "dunno";                                                                                
                }
            }


            $msg2 = "Marked-As-CLEAN/LEVEL: ".$custom_level;
            add_cache($DB,$sender,$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
            return "accept";
        }
        else
        {
            $msg2 = "No-Validations/LEVEL: ".$custom_level;
            return "accept"; //NO VALIDATIONS
        }
        /** DETECT INCOMING SPAM **/
    }
    /** VALIDATE **/
    ?>