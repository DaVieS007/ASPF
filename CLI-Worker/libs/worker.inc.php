<?php

    require "filter.inc.php";

    /** SESSION GLOBALS **/
    $peer_ip = NULL;
    $peer_name = NULL;
    $peer_port = NULL;

    $client_ip = NULL;
    $client_name = NULL;
    /** SESSION GLOBALS **/

    /** SAFE_SEND **/
    function safe_send($sock,&$data)
    {
        $tlen = strlen($data);
        $size = $tlen;
        while(true)
        {
            $len = socket_write($sock,$data);
            if($len == 0 || $len < 0)
            {
                return $len;
            }
            else if($len < $size)
            {
                $size = $size - $len;
                $data = substr($data,$len);
            }
            else if($len >= $size)
            {
                return $tlen;
            }
        }
    }
    /** SAFE_SEND **/

    /** WORKER **/
    function worker($client,$nodes)
    {
        global $config;

        /** GET PEER ADDRESS **/
        socket_getpeername ($client, $peer_ip, $peer_port);        
        $peer_name = gethostbyaddr($peer_ip);
        /** GET PEER ADDRESS **/

        $srv_info = array();
        $srv_info["peer_ip"] = $peer_ip;
        $srv_info["peer_port"] = $peer_port;
        $srv_info["peer_name"] = $peer_name;
        
        $buf = NULL;
        $write = NULL;
        $except = NULL;
        $FDSET = array($client);
        $FDSEL = socket_select($FDSET, $write, $except, $config["SERVER"]["recv_timeout"]);
        if(!$FDSEL)
        {
            mlog("Worker","NOTICE","RECV-TIMEOUT ...");
            safe_send($client,"recv-timeout\n");
            return false;
        }

        $read = socket_read($client,4096,PHP_BINARY_READ);

        $postfix_sign = "request=smtpd_access_policy";
        if(substr($read,0,strlen($postfix_sign)) == $postfix_sign)
        {
            mlog("Worker","NOTICE","Processing Access/Policy Request from: {".$peer_name."} ".$peer_ip.":".$peer_port);

            /** DATABASE CONNECTION **/
            $DB = new database_handler($config["DATABASE"]["mysql_host"],$config["DATABASE"]["mysql_user"],$config["DATABASE"]["mysql_password"],$config["DATABASE"]["mysql_database"]);
            if($DB->c_error)
            {
                mlog("Worker","ERROR","Could not connect to Database");
                if($config["SERVER"]["accept_on_failure"])
                {
                    mlog("Validate","NOTICE","[PASSED-ON-FAILURE] ".$arr["sender"]." -> ".$arr["recipient"]);
                    banner($client,"PASSED","Accept-On-Failure");
                    safe_send($client,"action=dunno\n");
                    safe_send($client,"\n");
                    return false;
                }
                else
                {
                    mlog("Validate","NOTICE","[DEFER-ON-FAILURE] ".$arr["sender"]." -> ".$arr["recipient"]);
                    safe_send($client,"action=DEFER ASPF Service is currently offline, try again later\n");
                    safe_send($client,"\n");
                    return false;
                }
            }
            /** DATABASE CONNECTION **/

            /** CHECK NODES **/
            $node = false;
            if(count($nodes))
            {
                while(list($k,$v) = each($nodes))
                {
                    if($v["ip4"] == $peer_ip)
                    {
                        $node = $v;
                    }
                    else if($v["ip6"] == $peer_ip)
                    {
                        $node = $v;
                    }
                }
            }

            if($config["SERVER"]["auto_accept_nodes"])
            {
                if(!$node)
                {
                    /** ADD NEWBIE TO NODES **/
                    $nconfig["SPAM_DETECT"] = $config["SPAM_DETECT"];
                    $nconfig["GRAYLIST"] = $config["GRAYLIST"];
                    $nconfig["ANTISPAM"] = $config["ANTISPAM"];
                    
                    if(strstr($peer_ip,":"))
                    {
                        $inet = 6;
                    }
                    else
                    {
                        $inet = 4;
                    }
                    /** ADD NEWBIE TO NODES **/

                    $current = $DB->query("SELECT * FROM nodes WHERE name = '".$DB->escape($peer_name)."'")->fetch_array();
                    if($current["ID"])
                    {
                        /** FILL IP-s **/
                        if($inet == 6)
                        {
                            if($current["ip6"] == "")
                            {
                                $DB->query("UPDATE nodes SET ip6 = '".$DB->escape($peer_ip)."' WHERE ID = '".$current["ID"]."'");
                            }
                            else if($current["ip4"] == "")
                            {
                                $DB->query("UPDATE nodes SET ip4 = '".$DB->escape($peer_ip)."' WHERE ID = '".$current["ID"]."'");
                            }
                        }
                        /** FILL IP-s **/
                    }
                    else
                    {
                        if($inet == 6)
                        {
                            $ip4 = "";
                            $ip6 = $peer_ip;
                        }
                        else
                        {
                            $ip4 = $peer_ip;
                            $ip6 = "";
                        }
                        
                        $DB->query("INSERT INTO nodes (ip4,ip6,name,settings,last_seen) VALUES ('".$DB->escape($ip4)."','".$DB->escape($ip6)."','".$DB->escape($peer_name)."','".$DB->escape(json_encode($nconfig))."','".time()."')");
                        mlog("Worker","NOTICE","Open-Mode Node Added: ".$peer_name);
                    }
                }
            }
            else if(!$node)
            {
                mlog("Worker","NOTICE","Client Dropped due not on the list: ".$peer_ip);
                safe_send($client,"not-allowed-here\n");
                return false;    
            }        

            if($node)
            {
                /** OVERRIDE **/
                $config["SPAM_DETECT"] = $node["settings"]["SPAM_DETECT"];
                $config["GRAYLIST"] = $node["settings"]["GRAYLIST"];
                $config["ANTISPAM"] = $node["settings"]["ANTISPAM"];
                /** OVERRIDE **/

                $DB->query("UPDATE nodes SET last_seen = '".time()."' WHERE ID = '".$node["ID"]."'");
            }
            /** CHECK NODES **/


            filter($DB,$client,$read,$srv_info);
        }
        else
        {
            safe_send($client,"invalid-command\n");
            return false;
        }
  
    }
    /** WORKER **/
?>