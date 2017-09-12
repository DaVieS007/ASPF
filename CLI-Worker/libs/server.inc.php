<?php

    /** START_SERVER **/
    function start_server($ip,$port,$inet)
    {
        if (($sock = socket_create($inet, SOCK_STREAM, SOL_TCP)) === false) 
        {
            mlog("Server","ERROR","Could not create socket: ".socket_strerror(socket_last_error()));
            return false;
        }
    
        socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
        if (socket_bind($sock, $ip, $port) === false) 
        {
            mlog("Server","ERROR","Could not bind socket: ".socket_strerror(socket_last_error($sock)));
            socket_close($sock);
            return false;
        }
    
        if (socket_listen($sock, 5) === false) 
        {
            mlog("Server","ERROR","Could not listen a socket: ".socket_strerror(socket_last_error($sock)));
            socket_close($sock);
            return false;
        }        

        mlog("Server","NOTICE","Waiting Connection at [".$ip."]:".$port);
        
        return $sock;
    }
    /** START_SERVER **/
?>