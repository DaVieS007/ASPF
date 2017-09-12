<?php

    require "validate.php";
    require "limit.php";
    
    /** BANNER **/
    function banner(&$client,$state,$message)
    {
        global $config;
        socket_write($client,"action=PREPEND X-ASPF: !".strtoupper($state)." (".$message.") | see: https://aspf.npulse.net\n");
    }

    function banner2(&$client,$state,$message)
    {
        global $config;
        socket_write($client,"action=PREPEND X-ASPF-S: !".strtoupper($state)." (".$message.") | see: https://aspf.npulse.net\n");
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
            $var[0] = trim($var[0]);
            $var[1] = trim($var[1]);
            $arr[$var[0]] = $var[1];
        }

        if($arr["sasl_username"])
        {
            $arr["real_sender"] = $arr["sasl_username"];            
        }
        else
        {
            $arr["real_sender"]  = $arr["sender"];
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

        if(trim($arr["sasl_username"]) != "" || trim($arr["exim_auth"]) != "" || $arr["server_port"] == "10025")
        {
            /** LIMIT OUTGOING MESSAGES **/
            $msg = NULL;
            $ret = limit($DB,$arr,$msg,$srv_info);
            if($ret == "sent")
            {
                mlog("Limit","NOTICE","Sent: ".$arr["real_sender"]." -> ".$arr["recipient"]);
                banner2($client,"PASSED","White");
                socket_write($client,"action=dunno\n");
                socket_write($client,"\n");
                return false;
            }
            else if($ret == "notify")
            {
                banner2($client,"PASSED","Gray");
                mlog("Limit","NOTICE","Limit Reached (Notify): ".$arr["real_sender"]." -> ".$arr["recipient"]);                
                socket_write($client,"action=dunno\n");
                socket_write($client,"\n");

                if($config["ANTISPAM"]["notify_command"])
                {
                    shell_exec($config["ANTISPAM"]["notify_command"]);                    
                }
                return false;
            }
            else if($ret == "reject")
            {
                mlog("Limit","NOTICE","Limit Reached (Reject): ".$arr["real_sender"]." -> ".$arr["recipient"]);                
                socket_write($client,"action=REJECT ".$msg."\n");
                socket_write($client,"\n");

                if($config["ANTISPAM"]["notify_command"])
                {
                    shell_exec($config["ANTISPAM"]["notify_command"]);                    
                }
                return false;                                    
            }
            /** LIMIT OUTGOING MESSAGES **/
        }
        else
        {
            /** VALIDATE INCOMING MESSAGES **/
            $msg = NULL;
            $msg2 = NULL;
            $ret = validate($DB,$arr,$msg,$msg2,$srv_info);
            add_transaction($DB,$arr["real_sender"],$arr["recipient"],$ret,$msg,$msg2,$srv_info["peer_ip"],$srv_info["peer_name"],$srv_info["client_ip"],$srv_info["client_name"]);
            if($ret == "dunno")
            {
                if($config["drop_mail_instead_of_mark_spam"])
                {
                    mlog("Validate","NOTICE","[".$msg."] ".$arr["sender"]." -> ".$arr["recipient"]);
                    socket_write($client,"action=REJECT ".$msg."\n");
                    socket_write($client,"\n");
                    return false;                        
                }
                else
                {
                    mlog("Validate","NOTICE","[".$msg2."] ".$arr["sender"]." -> ".$arr["recipient"]);
                    banner($client,"REJECT",$msg2);
                    socket_write($client,"action=dunno\n");
                    socket_write($client,"\n");
                    return false;                        
                }
            }
            else if($ret == "reject")
            {
                mlog("Validate","NOTICE","[REJECT] ".$arr["sender"]." -> ".$arr["recipient"]);
                socket_write($client,"action=REJECT ".$msg."\n");
                socket_write($client,"\n");
                return false;                                    
            }
            else if($ret == "accept")
            {
                mlog("Validate","NOTICE","[PASSED] ".$arr["sender"]." -> ".$arr["recipient"]);
                banner($client,"PASSED",$msg2);
                socket_write($client,"action=dunno\n");
                socket_write($client,"\n");
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