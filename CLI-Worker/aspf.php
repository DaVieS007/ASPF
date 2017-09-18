#!/usr/bin/php
<?php
    $DAEMONIZED = NULL;
    /** DAEMONIZE **/
    if($argv[1] == "-daemon")
    {
        umask(0);
	    $opid = getmypid();
        $DAEMONIZED = true;
        $pid = pcntl_fork();
        
	    if($pid)
	    {
		    exit;
        }
        elseif($pid == -1)
        {
            echo("Error: Could not fork();");
        }
        else
        {
            file_put_contents("/var/run/aspf.pid",posix_getpid());   
            $sid = posix_setsid(); //DETACH FROM SESSION
        }
    }
    /** DAEMONIZE **/

    require "libs/utils.inc.php";

    /** C_STANDARD/LET_IT_GLOBAL **/
    $config = array();
    $sconfig = array();
    $max_workers = 0;
    /** C_STANDARD/LET_IT_GLOBAL **/

    /** RETRIEVE_CONFIG **/
    function retrieve_config()
    {
        global $config;
        global $sconfig;
        $config = parse_ini_file("aspf.conf",true);    
        /** IF MVCP PRESENT WE PULL PASSWORDS FROM IT **/
        if($config["SERVER"]["mvcp_support"])
        {
            if(is_file("/Storage/System/Install/.config"))
            {
                $sconfig = unserialize(file_get_contents("/Storage/System/Install/.config"));
                if(is_array($sconfig))
                {
                    $config["DATABASE"]["mysql_host"] = "SQL";
                    $config["DATABASE"]["mysql_user"] = "mvcp_aspf";
                    $config["DATABASE"]["mysql_password"] = $sconfig["aspf_password"];
                    $config["DATABASE"]["mysql_database"] = "mvcp_aspf";
                    mlog("Init","NOTICE","ASPF-MVCP Configuration (Re)Loaded ..");
                    return true;
                }    
                else
                {
                    mlog("Init","WARNING","MVCP-Configuration Could not Loaded (Invalid: /Storage/System/Install/.config)");
                } 
            }    
            else
            {
                mlog("Init","WARNING","MVCP-Configuration Could not Loaded (Missing: /Storage/System/Install/.config)");
            } 
        }
        else
        {
            mlog("Init","NOTICE","ASPF Configuration (Re)Loaded ..");            
        }
        /** IF MVCP PRESENT WE PULL PASSWORDS FROM IT **/    
    }
    /** RETRIEVE_CONFIG **/

    retrieve_config();

    require "libs/server.inc.php";
    require "libs/worker.inc.php";
    require "libs/mysql.inc.php";

    $config["hostname"] = trim(shell_exec("/bin/hostname"));
    
    $workers = array();
    $socket_table = array();


    /** START_SERVERS **/
    if($config["SERVER"]["ip4_listen"])
    {
        $inet4 = start_server($config["SERVER"]["ip4_listen"],$config["SERVER"]["listen_port"],AF_INET);
        if(!$inet4)
        {
            mlog("Core","ERROR","Failed to Start Server on: ".$config["SERVER"]["ip4_listen"].":".$config["SERVER"]["listen_port"]);
        }
    }

    if($config["SERVER"]["ip6_listen"])
    {
        $inet6 = start_server($config["SERVER"]["ip6_listen"],$config["SERVER"]["listen_port"],AF_INET6);
        if(!$inet6)
        {
            mlog("Core","ERROR","Failed to Start Server on: ".$config["SERVER"]["ip6_listen"].":".$config["SERVER"]["listen_port"]);
        }
    }
    /** START_SERVERS **/

    /** FAULT_CHECK **/
    if(!$inet4 && !$inet6)
    {
        die();
    }

    if($DAEMONIZED)
    {
        $silent = true;
        fclose(STDIN);  
        fclose(STDOUT); 
        fclose(STDERR);
    }

    if($config["SERVER"]["max_workers"] < 1)
    {
        mlog("Core","ERROR","max_workers < 1");
        die();
    }
    /** FAULT_CHECK **/

    $MPID = posix_getpid();

    /** PROCESS_CLIENTS & ACCEPT_CONNECTIONS **/
    $clients = array();
    $nodes = array();
    $last_update = 0;

    while(true)
    {
        try
        {
            $buf = NULL;
            $write = NULL;
            $except = NULL;
            if($inet4)
            {
                $FDSET[] = $inet4;                
            }

            if($inet6)
            {
                $FDSET[] = $inet6;    
            }

            $FDSEL = socket_select($FDSET, $write, $except, 0,1000*10);
            if($FDSEL)
            {
                while(list($k,$sock) = each($FDSET))
                {
                    $clients[] = socket_accept($sock);
                }
            }
    
            /** STAT **/
            if(count($workers) > $max_workers)
            {
                $max_workers = count($workers);
            }
            /** STAT **/


            /** CHECK_PIDS **/
            $_workers = $workers;
            reset($_workers);
            while(list($k,$v) = each($_workers))
            {
                $res = pcntl_waitpid($v, $status, WNOHANG);
                
                // If the process has already exited
                if($res == -1 || $res > 0)
                {
                    unset($workers[$k]);         
                    socket_close($socket_table[$k]);
                    unset($socket_table[$k]);
                    mlog("Core","NOTICE","Worker-".$k." Exited ...");
                }
            }
    
            /** UPDATE_CONFIG **/
            if($last_update + 60 < time())
            {
                $stat = array();
                $stat["workers"]["current"] = $max_workers;
                $max_workers = 0;
                $stat["workers"]["max"] = $config["SERVER"]["max_workers"];
                update_state("workers",$stat);

                update_nodes($nodes);
                retrieve_config();
                $last_update = time();
            }
            /** UPDATE_CONFIG **/
        
            /** FORK **/
            if(count($clients) > 0 && count($workers) < $config["SERVER"]["max_workers"])
            {
                $_clients = $clients;
                reset($_clients);
                while(list($k,$v) = each($_clients))
                {
                    $client = $v;
                    if(count($workers) < $config["SERVER"]["max_workers"])
                    {
                        $wid = worker_id($workers);
                        $socket_table[$wid] = $client;
                        
                        $pid = pcntl_fork();
                        if ($pid == -1) 
                        {
                             mlog("Core","ERROR","Could not fork()");
                        } 
                        else if ($pid) 
                        {
                            $workers[$wid] = $pid;
                            mlog("Core","NOTICE","Worker-".$wid." Launched ...");
                            unset($clients[$k]);
                        } 
                        else 
                        {
                            /** SWITCH USER **/
                            posix_setuid(posix_getpwnam($config["SERVER"]["user"])["uid"]);
                            posix_setgid(posix_getgrnam($config["SERVER"]["group"])["gid"]);
                            /** SWITCH USER **/
                        
                            worker($client,$nodes);
                            die();
                        }
                    }
                }
            }
            /** FORK **/
        }
        catch (Throwable $t)
        {
           mlog("Core","PANIC",$t->getFile().":".$t->getLine().": ".$t->getMessage());
		   if($MPID != posix_getpid())
		   {
			   die();
		   }           
        }        
        catch(Exception $e)
        {
            mlog("Core","PANIC",$t->getFile().":".$t->getLine().": ".$t->getMessage());
            if($MPID != posix_getpid())
            {
                die();
            }           
         }
    }
    /** PROCESS_CLIENTS & ACCEPT_CONNECTIONS **/
?>