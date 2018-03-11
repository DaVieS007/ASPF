<?php

    /** UPDATE_STATE **/
    function update_state($key,$arr)
    {
        global $config;

        $DB = new database_handler($config["DATABASE"]["mysql_host"],$config["DATABASE"]["mysql_user"],$config["DATABASE"]["mysql_password"],$config["DATABASE"]["mysql_database"]);
        if($DB->c_error)
        {
            mlog("UpdateState","ERROR","Could not connect to Database");
            return false;
        }

        $current = $DB->query("SELECT `key` FROM state WHERE `key` = '".$DB->escape($key)."'")->fetch_array();
        if($current["key"])
        {
            $DB->query("UPDATE state SET data = '".json_encode($arr)."', tstamp = '".time()."' WHERE `key` = '".$DB->escape($key)."'");
        }
        else
        {
            $DB->query("INSERT INTO state (`key`,tstamp,data) VALUES ('".$DB->escape($key)."','".time()."','".$DB->escape(json_encode($arr))."');");
        }

        return true;
    }
    /** UPDATE_STATE **/

    /** UPDATE_NODES **/
    function update_nodes(&$nodes)
    {
        global $config;

        $_nodes = array();
        $DB = new database_handler($config["DATABASE"]["mysql_host"],$config["DATABASE"]["mysql_user"],$config["DATABASE"]["mysql_password"],$config["DATABASE"]["mysql_database"]);
        if($DB->c_error)
        {
            mlog("NodeUpdate","ERROR","Could not connect to Database");
            return false;
        }

        $res = $DB->query("SELECT * FROM nodes");
        while($row = $res->fetch_array())
        {
            $row["settings"] = json_decode($row["settings"],true);
            $_nodes[$row["ID"]] = $row;
        }

        mlog("NodeUpdate","NOTICE","Retrieved ".count($_nodes)." node config(s)");
        $nodes = $_nodes;
        
        return true;
    }
    /** UPDATE_NODES **/

    /** MLOG **/
    function mlog($node,$type,$message)
    {
        global $config;
        global $silent;

	$tmp = date($config["SERVER"]["date_format"],time())." | [".$node."] ".$message."\n";
	file_put_contents("/var/log/aspf.log",$tmp,FILE_APPEND);
        if(!$silent)
        {
            echo($tmp);    
        }
    }
    /** MLOG **/

    /** WORKER_ID **/
    function worker_id(&$workers)
    {
        $i = 0;
        while(true)
        {
            if(!isset($workers[$i]))
            {
                return $i;
            }
            $i++;
        }
    }
    /** WORKER_ID **/

    /** SDATE **/
    function sdate($ts)
    {
        global $config;
        if($ts == 0)
        {
            return "Never Expire";
        }
        
        return date($config["SERVER"]["date_format"],$ts);
    }
    /** SDATE **/

    /** __GETHOSTBYNAME **/
    function __gethostbyname($host) 
    {
        $dns6 = dns_get_record($host, DNS_AAAA);
        $dns4 = dns_get_record($host, DNS_A);

        if(!is_array($dns6))
        {
            $dns6 = array();
        }

        if(!is_array($dns4))
        {
            $dns4 = array();
        }

        $dns = array_merge($dns4, $dns6);

        $ip6 = array();
        $ip4 = array();
        foreach ($dns as $record) 
        {
            if ($record["type"] == "A") 
            {
                $ip4[] = $record["ip"];
            }

            if ($record["type"] == "AAAA") 
            {
                $ip6[] = $record["ipv6"];
            }
        }

        $ret = array();
        $ret[4] = $ip4;
        $ret[6] = $ip6;
        return $ret;
    }
    /** __GETHOSTBYNAME **/

    /** SOCKET_TIMEOUT **/
    function socket_timeout($socket,$timeout)
    {
        $buf = NULL;
        $write = NULL;
        $except = NULL;
        $FDSET = array($socket);
        $FDSEL = socket_select($FDSET, $write, $except, 0,$timeout*1000);
        if($FDSEL)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /** SOCKET_TIMEOUT **/

    /** PROBE_MAIL **/
    function probe_mail(&$sender,&$recipient,&$mx)
    {
        global $config;
        $type = substr($mx,0,1);
        $ip = substr($mx,1);

        if($type == "4")
        {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);            
            socket_set_nonblock($socket);
        }
        else if($type == "6")
        {
            $socket = socket_create(AF_INET6, SOCK_STREAM, SOL_TCP);                        
            socket_set_nonblock($socket);
        }

        /** CONNECT **/
        $cc = 5;
        $connected = false;
        while($cc > 0)
        {
            if(@socket_connect($socket, $ip, 25) || socket_last_error() == 56)
            {
                socket_set_block($socket);
                $connected = true;
                break;
            }

            $cc--;
            sleep(1);
        }
        /** CONNECT **/

        /** WAIT FOR BANNER **/
        if(!socket_timeout($socket,5000))
        {
            socket_close($socket);
            return "timeout";
        }

        $banner = socket_read($socket,4096,PHP_BINARY_READ);
        /** WAIT FOR BANNER **/

        socket_write($socket,"EHLO ".explode("@",$recipient)[1]."\r\n"); //SEND HELO

        /** WAIT FOR CAPABILITY **/
        if(!socket_timeout($socket,5000))
        {
            socket_close($socket);
            return "timeout";
        }
        $capabilities = socket_read($socket,4096,PHP_BINARY_READ);
        /** WAIT FOR CAPABILITY **/

        socket_write($socket,"MAIL FROM: <".$recipient.">"."\r\n"); //MAIL FROM
        /** WAIT FOR ANSWER **/
        if(!socket_timeout($socket,5000))
        {
            socket_close($socket);
            return "timeout";
        }
        $cmd = socket_read($socket,4096,PHP_BINARY_READ);
        if(substr($cmd,0,3) != "250")
        {
            return "issue";
        }
        /** WAIT FOR ANSWER **/

        socket_write($socket,"RCPT TO: <".$sender.">"."\r\n"); //RCPT TO
        /** WAIT FOR ANSWER **/
        if(!socket_timeout($socket,30000))
        {
            socket_close($socket);
            return "timeout";
        }
        $cmd = socket_read($socket,4096,PHP_BINARY_READ);
        if(substr($cmd,0,3) != "250")
        {
            var_dump($cmd);
            socket_close($socket);
            return "fake";
        }
        else
        {
            socket_close($socket);
            return "ok";
        }
        /** WAIT FOR ANSWER **/
    }
    /** PROBE_MAIL **/

    /** rbl_check **/
    function rbl_check($ip)
    {
        global $config;

        $rbls = explode(",",str_replace(" ","",$config["SPAM_DETECT"]["rbl_list"].","));
		$reversedIp = implode('.', array_reverse(explode ('.', $ip)));

		while(list($k,$dnsbl) = each($rbls))
		{
			if($dnsbl)
			{
                if(checkdnsrr($reversedIp.".".$dnsbl.".","A"))
                {
                    return $dnsbl;
                }
			}
		}
		return false;
    }
    /** rbl_check **/

    /** OS_PROBE **/
    function os_probe($ip)
    {
        $ports = "80,443,587,465,110,143,993,995,21";
        $ports = explode(",",$ports);
        $max = count($ports);
        $ip = gethostbyname($ip);
        $ret = array();

        if(strstr($ip,":"))
        {
            $type = 6;
        }
        else
        {
            $type = 4;
        }

        /** PREPARE **/
        $socket = array();
        reset($ports);
        while(list($k,$v) = each($ports))
        {
            $port = $v;
            if(!isset($socket[$port]))
            {
                if($type == 4)
                {
                    $socket[$port] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);                                                        
                }
                else
                {
                    $socket[$port] = socket_create(AF_INET6, SOCK_STREAM, SOL_TCP);                                                        
                }

                socket_set_nonblock($socket[$port]);
                socket_connect($socket[$port], $ip, $port);
            }
        }
        /** PREPARE **/
        
        $found_open = false;
        sleep(5);

        while(list($port,$sock) = each($socket))
        {
            if(@socket_connect($sock, $ip, $port) || socket_last_error() == 56)
            {
                $ret[] = $port;
            }
            socket_close($sock);
        }

        $score = round((100 / $max)*count($ret),2);

        return $score;        
    }
    /** OS_PROBE **/

    /** PROBE_MX **/
    function probe_mx(&$sender,&$mxes)
    {
        /** PREPARE **/
        $socket4 = array();
        $socket6 = array();
        reset($mxes);
        $limit = 5;
        while(list($k,$v) = each($mxes))
        {
            $cc = 0;
            $arr = __gethostbyname($v);
            while(list($k,$ip) = each($arr[4]))
            {
                if(!isset($socket4[$ip]))
                {
                    if($cc >= 5)
                    {
                        break;
                    }
                    $socket4[$ip] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);                                    
                    socket_set_nonblock($socket4[$ip]);
                    socket_connect($socket4[$ip], $ip, 25);

                    $cc++;
                }
            }

            $cc = 0;
            while(list($k,$ip) = each($arr[6]))
            {
                if(!isset($socket6[$ip]))
                {
                    $cc++;
                    $socket6[$ip] = socket_create(AF_INET6, SOCK_STREAM, SOL_TCP);                                    
                    socket_set_nonblock($socket6[$ip]);
                    socket_connect($socket6[$ip], $ip, 25);
                }
            }
        }
        /** PREPARE **/
        
        $found_open = false;
        $cc = 5; //About 5 Seconds is pretty enough to connect in all cases

        /** PROCESS **/
        while($cc > 0 && !$found_open)
        {
            reset($socket6);
            while(list($ip,$sock) = each($socket6))
            {
                if(@socket_connect($sock, $ip, 25) || socket_last_error() == 56)
                {
                    $found_open = "6".$ip;
                    break;
                }
            }

            if(!$found_open)
            {
                reset($socket4);
                while(list($ip,$sock) = each($socket4))
                {
                    if(@socket_connect($sock, $ip, 25) || socket_last_error() == 56)
                    {
                        $found_open = "4".$ip;
                        break;
                    }
                }    
            }
    
            sleep(1);
            $cc--;
        }
        /** PROCESS **/

        /** CLEANUP **/
        reset($socket4);
        while(list($ip,$sock) = each($socket4))
        {
            socket_close($sock);
        }

        reset($socket6);
        while(list($ip,$sock) = each($socket6))
        {
            socket_close($sock);
        }
        /** CLEANUP **/

        return $found_open;
    }
    /** PROBE_MX **/

    /** ADD_TRANSACTION **/
    function add_transaction(&$DB,$real_sender,$recipient,$action,$msg,$msg2,$smtp_ip,$smtp_reverse,$client_ip,$client_reverse)
    {
        $DB->query("INSERT INTO transactions (sender,recipient,action,message,message2,smtp_ip,smtp_name,sender_ip,sender_name,tstamp) VALUES ('".$DB->escape($real_sender)."','".$DB->escape($recipient)."','".$DB->escape($action)."','".$DB->escape($msg)."','".$DB->escape($msg2)."','".$DB->escape($smtp_ip)."','".$DB->escape($smtp_reverse)."','".$DB->escape($client_ip)."','".$DB->escape($client_reverse)."','".time()."');");
    }
    /** ADD_TRANSACTION **/

    /** ADD_EXTERNAL_CACHE **/
    function add_cache(&$DB,$sender,$smtp_ip,$smtp_reverse,$client_ip,$client_reverse)
    {
        global $config;

        $cache_until = time() + $config["GRAYLIST"]["gray_cache_expire"] * 60 * 60 * 24;
        $row = $DB->query("SELECT * FROM senders WHERE address = '".$DB->escape($sender)."'")->fetch_array();
        if($row["ID"])
        {
            if($row["type"] == "cache")
            {
                $DB->query("UPDATE senders SET smtp_ip = '".$DB->escape($smtp_ip)."', smtp_name = '".$DB->escape($smtp_reverse)."', sender_ip = '".$DB->escape($client_ip)."', sender_name = '".$DB->escape($client_reverse)."', expire = '".$cache_until."' WHERE ID = '".$row["ID"]."'");
            }
        }
        else
        {
            $DB->query("INSERT INTO senders (smtp_ip,smtp_name,sender_ip,sender_name,address,`type`,expire) VALUES ('".$DB->escape($smtp_ip)."','".$DB->escape($smtp_reverse)."','".$DB->escape($client_ip)."','".$DB->escape($client_reverse)."','".$DB->escape($sender)."','cache','".$cache_until."');");
        }
    }
    /** ADD_EXTERNAL_CACHE **/

    /** LEARN_DOMAIN **/
    function learn_domain(&$DB,$domain)
    {
        global $config;

        $cache_until = time() + $config["GRAYLIST"]["gray_learn_expire"] * 60 * 60 * 24;
        $row = $DB->query("SELECT * FROM domains WHERE domain = '".$DB->escape($domain)."'")->fetch_array();
        if($row["ID"])
        {
            if($row["type"] != "blacklist" && $row["type"] != "whitelist" && $row["type"] != "auto-whitelist")
            {
                $DB->query("UPDATE domains SET type = 'auto-whitelist', expire = '".$cache_until."' WHERE ID = '".$row["ID"]."'");
                return true;
            }
        }
        else
        {
            $DB->query("INSERT INTO domains (domain,`type`,expire) VALUES ('".$DB->escape($domain)."','auto-whitelist','".$cache_until."');");
            return true;
        }
        return false;
    }
    /** LEARN_DOMAIN **/

    /** LEARN_MAIL **/
    function learn_mail(&$DB,$mail)
    {
        global $config;

        $cache_until = time() + $config["GRAYLIST"]["gray_learn_expire"] * 60 * 60 * 24;
        $row = $DB->query("SELECT * FROM senders WHERE address = '".$DB->escape($mail)."'")->fetch_array();
        if($row["ID"])
        {
            if($row["type"] != "blacklist" && $row["type"] != "whitelist" && $row["type"] != "auto-whitelist")
            {
                $DB->query("UPDATE senders SET type = 'auto-whitelist', expire = '".$cache_until."' WHERE ID = '".$row["ID"]."'");
                return true;
            }
        }
        else
        {
            $DB->query("INSERT INTO senders (address,`type`,expire) VALUES ('".$DB->escape($mail)."','auto-whitelist','".$cache_until."');");
            return true;
        }
        return false;
    }
    /** LEARN_MAIL **/    

?>
