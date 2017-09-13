<?php
    /** MAILB **/
    function mailb($text,$mlen = 40)
    {
        if(strlen($text) > $mlen)
        {
            if(strstr($text,"="))
            {
                $text = explode("=",$text);
                return mailb($text[count($text) - 1]);
            }

            $a = substr($text,0,6);
            $cut = strlen($text) - $mlen;
            $b = substr($text,6+$cut + 4);
            return $a."[..]".$b;
        }

        return $text;
    }
    /** MAILB **/

    /** BYTER **/
    function Byter($bytes, $precision = 2) 
    { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow)); 

        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 
    /** BYTER **/



    /** SDATE **/
    function sdate($tstamp = -1)
    {
        global $config;
        global $auth;

        if($tstamp == -1)
        {
            $tstamp = time();
        }
        elseif($tstamp == 0)
        {
            return " --- ";
        }  


        $dtz = new DateTimeZone(date_default_timezone_get());
        $servertime = new DateTime('now', $dtz);
        $offset = $dtz->getOffset( $servertime );
        $tstamp -= $offset;

        $dtz = new DateTimeZone($auth->user["timezone"]);
        $relatime = new DateTime('now', $dtz);
        $offset = $dtz->getOffset( $relatime );
        $tstamp += $offset;
        //return $offset;

        return date($config["date_format"],$tstamp);
    }
    /** SDATE **/

?>