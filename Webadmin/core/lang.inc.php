<?php

    $L = array();
    
    /** L **/
    function L($prefix)
    {
        global $L;
        $ret = $L[strtolower($prefix)];
        if(!$ret)
        {
            $ret = $prefix;
        }
        return $ret;
    }
    /** L **/

    /** LL **/
    function LL($prefix)
    {
        global $L;
        $ret = "{!LANG:".strtoupper($prefix)."}";

        return $ret;
    }
    /** L **/


    class _lang
    {

        var $curlang;

        function prefered_language(array $available_languages, $http_accept_language) {

            $available_languages = array_flip($available_languages);

            preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
            foreach($matches as $match) {

                list($a, $b) = explode('-', $match[1]) + array('', '');
                $value = isset($match[2]) ? (float) $match[2] : 1.0;

                if(isset($available_languages[$match[1]])) {
                    $langs[$match[1]] = $value;
                    continue;
                }

                if(isset($available_languages[$a])) {
                    $langs[$a] = $value - 0.1;
                }

            }
            arsort($langs);

            return $langs;
        }

        function init()
        {
            global $config; global $url;

            $URL = $url->get();

            reset($config["langs"]);
            while(list($k,$v) = each($config["langs"]))
            {
                if($v == $URL[0])
                {
                    $this->curlang = $v;
                    break;
                }
            }

            $cookie_lang = $_COOKIE["force_lang"];
            if($this->curlang == NULL && isset($cookie_lang))
            {
                $found = false;
                reset($config["langs"]);
                while(list($k,$v) = each($config["langs"]))
                {
                    if($cookie_lang == $v)
                    {
                        $this->curlang = $v;
                        break;
                    }
                }
            }

            if($this->curlang == NULL)
            {
                $available_languages = $config["langs"];
                $langs = $this->prefered_language($config["langs"], $_SERVER["HTTP_ACCEPT_LANGUAGE"]);

                while(list($k,$v) = each($langs))
                {
                    if($this->curlang == NULL)
                    {
                        reset($config["langs"]);
                        while(list($k2,$v2) = each($config["langs"]))
                        {
                            if($k == $v2)
                            {
                                $this->curlang = $v2;
                                break;
                            }
                        }                        
                    }
                }

                if($this->curlang == NULL)
                {
                    $this->curlang = $config["default_language"];
                }
            }

            if($_COOKIE["force_lang"] != $this->curlang)
            {
                setcookie("force_lang",$this->curlang,time() + 365 * 60 * 60 * 24,"/");
            }

            if($URL[0] != $this->curlang)
            {
                $nurl = $URL;
                $nurl[0] = $this->curlang;
                $url->go($nurl);                
            }
        }
    }

?>