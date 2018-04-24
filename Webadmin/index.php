<?php
    error_reporting(E_ALL);
    /** FORCE_SSL **/
    if(!$_SERVER["HTTPS"])
    {
        header("Location: https://".$_SERVER["HTTP_HOST"]);
        die();
    }
    /** FORCE_SSL **/

    /** GLOBALS **/
    $DB = NULL;
    $syslog = NULL;

    require "config.inc.php";
    require "core/url.inc.php";
    require "core/mysql.inc.php";
    require "core/lang.inc.php";
    require "core/registry_fs.inc.php";
    require "core/punycode.inc.php";
    require "core/auth_simple.inc.php";
    require "widgets/core.php";
    require "utils.php";


    $url = new _url();
    $URL = $url->get();
    
    if(isset($_GET["lang"]))
    {
        $URL[0] = $_GET["lang"];
        $url->go($URL);
    }
    
    $DB = new database_handler($config["mysql_host"],$config["mysql_user"],$config["mysql_passwd"],$config["mysql_db"]);

    $registry = new registry_fs("registry.db");
    $punycode = new Punycode();
    $widget = new widget();
    $registry->cleanup();
    $auth = new auth_simple($registry);


    $lang = new _lang();
    $lang->init();

    require "core/lang.".$lang->curlang.".php";
    

    $content = "";
    $RET = "";

    if($URL[1] == "login")
    {
        if($auth->login($URL[2]))
        {
            $RET = "<script>self.location.href='/';</script>";
        }
        else
        {
            $RET = L("LOGIN_FAILED");
        }
    }
    elseif($URL[1] == "logout")
    {
        $auth->logout();
    }
    else
    {
        if(!$auth->check())
        {
            require "pages/auth/core.php";            
        }
        else
        {
            if(isset($_GET["fsearch"]) && $_GET["fsearch"])
            {
                $url->go(array($URL[0],"search",urlencode($_GET["fsearch"])));
            }
                
            require "pages/main/core.php";
        }
    }

    /** PARSE OUTPUT **/
    preg_match_all("|{!.*}|U",$RET,$out, PREG_PATTERN_ORDER);
    $tmp = $out[0];
    while(list($k,$v) = each($tmp))
    {
        $key = $v;
        $v = str_replace("{","",$v);
        $v = str_replace("}","",$v);
        $v = str_replace("!","",$v);
        $ex = explode(":",$v);
        if($ex[0] == "HTML")
        {
            $RET = str_replace($key,file_get_contents("common_html/".strtolower($ex[1]).".html"),$RET);
        }
        elseif($ex[0] == "LANG")
        {
            $RET = str_replace($key,L(strtolower($ex[1])),$RET);
        }
        elseif($ex[0] == "USER")
        {
            $RET = str_replace($key,$auth->user[strtolower($ex[1])],$RET);
        }
    }

    /** PARSE OUTPUT **/
    echo($RET);
?>
