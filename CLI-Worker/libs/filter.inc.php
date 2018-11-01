<?php

    require "validate.php";
    require "limit.php";
    
    /** BANNER **/
    function banner(&$client,$state,$message)
    {
        global $config;
        $data = "action=PREPEND X-ASPF: !".strtoupper($state)." (".$message.") | see: https://npulse.net/aspf\n";
        safe_send($client,$data);
    }

    function banner2(&$client,$state,$message)
    {
        global $config;
        safe_send($client,"action=PREPEND X-ASPF-S: !".strtoupper($state)." (".$message.") | see: https://npulse.net/aspf\n");
    }
    /** BANNER **/

    /** FILTER **/
    function filter(&$DB,&$client,&$data,&$srv_info)
    {
        global $config;


        $start = microtime();

        /** PARSE INPUT **/
        $tmp = explode("\n",$data);
        $arr = array();

        while(list($k,$v) = each($tmp))
        {
            $var = explode("=",$v,2);
            if(!isset($var[0]) || !isset($var[1]))
            {
                continue;
            }

            $var[0] = trim($var[0]);
            $var[1] = trim($var[1]);
            $arr[$var[0]] = $var[1];
        }

        /** utc=utc@domain.ex **/
        /** SOME MAKEUP **/
        if(strstr($arr["sender"],"="))
        {
            $tmp = explode("=",$arr["sender"]);
            while(list($k,$v) = each($tmp))
            {
                if(strstr($v,"@"))
                {
                    $arr["sender"] = $v;
                    break;
                }
            }
        }

        if(isset($arr["sasl_username"]) && $arr["sasl_username"])
        {
            $arr["real_sender"] = $arr["sasl_username"];            
        }
        else
        {
            $arr["real_sender"]  = $arr["sender"];
        }


        if(strstr($arr["recipient"],"="))
        {
            $tmp = explode("=",$arr["recipient"]);
            while(list($k,$v) = each($tmp))
            {
                if(strstr($v,"@"))
                {
                    $arr["recipient"] = $v;
                    break;
                }
            }
        }
        /** SOME MAKEUP **/
        /** PARSE INPUT **/
        
        /** 
            sasl_username = set if came from postfix (authed)
            exim_auth=plain = limit
            server_port == 10025 when it came from postfix MTA
        **/

        if((isset($arr["sasl_username"]) && trim($arr["sasl_username"]) != "") || (isset($arr["exim_auth"]) && trim($arr["exim_auth"]) != "") || (isset($arr["server_port"]) && $arr["server_port"] == "10025"))
        {
            /** DUNNO **/
            if(!trim($arr["real_sender"]))
            {
                banner2($client,"DUNNO","No-Valid-Sender");
                safe_send($client,"action=dunno\n");
                safe_send($client,"\n");        
                return false;
            }
            /** DUNNO **/

            /** LIMIT OUTGOING MESSAGES **/
            $msg = NULL;
            $ret = limit($DB,$arr,$msg,$srv_info);
            if($ret == "sent")
            {
                mlog("Limit","NOTICE","Sent: ".$arr["real_sender"]." -> ".$arr["recipient"]);
                banner2($client,"PASSED","White");
                safe_send($client,"action=dunno\n");
                safe_send($client,"\n");
                return false;
            }
            else if($ret == "notify")
            {
                $arr["answer"] = $msg;
                banner2($client,"PASSED","Gray");
                mlog("Limit","NOTICE","Limit Reached (Notify): ".$arr["real_sender"]." -> ".$arr["recipient"]);                
                safe_send($client,"action=dunno\n");
                safe_send($client,"\n");
                
                if($config["ANTISPAM"]["notify_command"])
                {
                    notify($DB,$config["ANTISPAM"]["notify_command"],$ret,$arr,$srv_info);
                }

                return false;
            }
            else if($ret == "reject" || $ret == "reject-domain" || $ret == "blacklist")
            {
                if($ret == "blacklist")
                {
                    mlog("Limit","NOTICE","Sender Blacklisted (Reject): ".$arr["real_sender"]." -> ".$arr["recipient"]);                
                }
                else if($ret == "reject-domain")
                {
                    mlog("Limit","NOTICE","Sender IP Limit Reached (BlackList Whole Domain): ".$arr["real_sender"]." -> ".$arr["recipient"]);                                    
                }
                else
                {
                    mlog("Limit","NOTICE","Sender Limit Reached (Reject): ".$arr["real_sender"]." -> ".$arr["recipient"]);                                                        
                }

                safe_send($client,"action=REJECT ".$msg."\n");
                safe_send($client,"\n");

                if($ret == "reject-domain" || $ret == "blacklist")
                {
                    $domain = explode("@",$arr["real_sender"])[1];
                    $ts = time() + 60*15; //15 MINS
                    
                    $bl = $DB->query("SELECT * FROM domains WHERE domain = '".$DB->escape($domain)."'")->fetch_array(MYSQLI_ASSOC);
                    if(!$bl)
                    {
                        mlog("Limit","NOTICE","Sender Domain Blacklisted: ".$domain);                                    
                        $DB->query("INSERT INTO domains (domain,type,expire) VALUES ('".$DB->escape($domain)."','blacklist','".$ts."');");
                    }
                    else
                    {
                        if($bl["type"] != "whitelist" && $bl["expire"] < $ts)
                        {
                            $DB->query("UPDATE domains SET type = 'blacklist', expire = '".$ts."' WHERE domain = '".$DB->escape($domain)."'");    
                        }
                    }    
                }

                if($ret != "blacklist")
                {
                    if($config["ANTISPAM"]["notify_command"])
                    {
                        $arr["answer"] = $msg;
                        notify($DB,$config["ANTISPAM"]["notify_command"],$ret,$arr,$srv_info);
                    }
                }
                return false;                                    
            }
            /** LIMIT OUTGOING MESSAGES **/
        }
        else
        {
            /** DUNNO **/
            if(!trim($arr["real_sender"]))
            {
                mlog("Validate","NOTICE","No Valid Sender, Using HELO_NAME");                

                $hname = explode(".",$arr["helo_name"]);
                $final_host = NULL;
                $have_soa = false;
                while(true)
                {                    
                    $final_host = implode(".",$hname);
                    if(count($hname) > 2)
                    {
                        $SOA = dns_get_record ($final_host, DNS_SOA);
                        if(count($SOA) > 0)
                        {
                            $tmp = $hname;
                            $hname = array();
                            for($i=1;$i<count($tmp);$i++)
                            {
                                $hname[] = $tmp[$i];
                            }

                            continue;
                        }    
                        else
                        {
                            $have_soa = true;
                            break;
                        }
                    }
                    else
                    {
                        $SOA = dns_get_record ($final_host, DNS_SOA);
                        if(count($SOA) > 0)
                        {
                            $have_soa = true;
                        }
                        break;
                    }
                }

                if($have_soa)
                {
                    mlog("Validate","NOTICE","Found SOA on interim domain: ".$final_host);                
                }
                else
                {
                    mlog("Validate","NOTICE","Not Found SOA but using interim domain: ".$final_host);                                    
                }

                $arr["sender"] = "<bounce>@".$final_host;
                $arr["real_sender"] = "<bounce>@".$final_host;
                $arr["bounce"] = true;
            }
            else
            {
                $arr["bounce"] = false;
            }
            /** DUNNO **/


            /** VALIDATE INCOMING MESSAGES **/
            $msg = NULL;
            $msg2 = NULL;
            $ret = validate($DB,$arr,$msg,$msg2,$srv_info);
            if($ret == "dunno")
            {
                if($config["SPAM_DETECT"]["drop_mail_instead_of_mark_spam"])
                {
                    mlog("Validate","NOTICE","[".$msg."] ".$arr["real_sender"]." -> ".$arr["recipient"]);
                    safe_send($client,"action=REJECT ".$msg."\n");
                    safe_send($client,"\n");
                    add_transaction($DB,$arr["real_sender"],$arr["recipient"],$ret,$msg,$msg2,$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
                    return false;                        
                }
                else
                {
                    mlog("Validate","NOTICE","[".$msg2."] ".$arr["real_sender"]." -> ".$arr["recipient"]);
                    banner($client,"REJECT",$msg2);
                    safe_send($client,"action=dunno\n");
                    safe_send($client,"\n");
                    add_transaction($DB,$arr["real_sender"],$arr["recipient"],$ret,$msg,$msg2,$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
                    return false;                        
                }
            }
            else if($ret == "reject")
            {
                mlog("Validate","NOTICE","[REJECT] ".$arr["real_sender"]." -> ".$arr["recipient"]);
                safe_send($client,"action=REJECT ".$msg."\n");
                safe_send($client,"\n");
                add_transaction($DB,$arr["real_sender"],$arr["recipient"],$ret,$msg,$msg2,$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
                return false;                                    
            }
            else if($ret == "accept")
            {
                mlog("Validate","NOTICE","[PASSED] ".$arr["real_sender"]." -> ".$arr["recipient"]);
                banner($client,"PASSED",$msg2);
                safe_send($client,"action=dunno\n");
                safe_send($client,"\n");
                add_transaction($DB,$arr["real_sender"],$arr["recipient"],$ret,$msg,$msg2,$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
                return false;                        
            }
            else if($ret == "grey")
            {
                mlog("Validate","NOTICE","[GREYED] ".$arr["real_sender"]." -> ".$arr["recipient"]);
                safe_send($client,"action=defer_if_permit ".$msg."\n");
                safe_send($client,"\n");
                add_transaction($DB,$arr["real_sender"],$arr["recipient"],$ret,$msg,$msg2,$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
                return false;                        
            }
            /** VALIDATE INCOMING MESSAGES **/            
    }
            
/*
    request=smtpd_access_policy
    protocol_state=RCPT
    protocol_name=ESMTP
    client_address=37.221.209.64
    client_name=web.npulse.net
    client_port=48521
    reverse_client_name=web.npulse.net
    server_address=37.221.209.67
    server_port=25
    helo_name=web.npulse.net
    sender=davies@npulse.net
    recipient=davies@impulsive.hu
    recipient_count=0
    queue_id=
    instance=100b8.59a471b5.5f0ac.0
    size=1056
    etrn_domain=
    stress=
    sasl_method=
    sasl_username=
    sasl_sender=
    ccert_subject=
    ccert_issuer=
    ccert_fingerprint=
    ccert_pubkey_fingerprint=
    encryption_protocol=TLSv1.2
    encryption_cipher=AECDH-AES256-SHA
    encryption_keysize=256
    policy_context=

        ; Incoming SPAM Mitigation
        ; Level 1: Mark as SPAM when: Hostname and DNS are incomplete
        ; Level 2: Mark as SPAM when: Domain are not found in DNS
        ; Level 3: Mark as SPAM when: Reverse Domain is not equal with HELO/EHLO
        ; Level 4: Mark as SPAM when: When sender SMTP Server (MX) is not open for incoming messages
        ; Level 5: Mark as SPAM when: When sender address is not found on remote SMTP
        ; Level 6: Mark as SPAM when: Contains noreply@
*/        

        $end = microtime();
    }
    /** FILTER **/
?>