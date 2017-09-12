<?php

    /** MUTEX **/
    function mutex()
    {
        if(!is_file(".mutexer"))
        {
            file_put_contents(".mutexer", time());
        }

        $fp = fopen(".mutexer", "r+");
        while(true)
        {
            if (flock($fp, LOCK_EX)) 
            {
                $cur = trim(fread($fp,1024));
                while($cur == time())
                {
                    sleep(1);
                }

                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, time());
                fflush($fp);            // flush output before releasing the lock
                flock($fp, LOCK_UN);    // release the lock
                break;
            } 

            sleep(1);
        }
            fclose($fp);        
    }
    /** MUTEX **/

    /** SHIFT_TYPE **/
    function shift_type($ts)
    {
        global $registry;
        $shift_reg = unserialize($registry->query("0","shift"));

        $shifts = array();
        while(list($k,$v) = each($shift_reg))
        {
            $shifts[strtotime("2010-01-01 ".$v.":00")] = $k;
            $shifts[strtotime("2010-01-02 ".$v.":00")] = $k;
            $shifts[strtotime("2010-01-03 ".$v.":00")] = $k;
        }

        $delta = $ts;
        $hdate = date("H",$delta);
        $close = strtotime("2010-01-02 ".$hdate.":00:00");

        krsort($shifts);
        $last = 0;
        while(list($ts,$ret) = each($shifts))
        {
            if($ts <= $close)
            {
                return $ret;
            }
        }
    }
    /** SHIFT_TYPE **/

    /** _LOG **/
    function ADMIN_LOG($name,$descr)
    {
        _LOG("admin",$name,$descr);
    }

    function _LOG($type,$name,$descr)
    {
        global $auth;
        global $DB;

        $DB->query("INSERT INTO log (name,descr,user_id,user,type,tstamp) VALUES ('".$DB->escape($name)."','".$DB->escape($descr)."','".$DB->escape($auth->user["ID"])."','".$DB->escape($auth->user["name"])."','".$type."','".time()."');");
    }
    /** _LOG **/

    /** STATES **/
    function states($key,$data = NULL)
    {
        global $DB;

        if($data)
        {
            $DB->query("INSERT IGNORE INTO states (ID,data) VALUES ('".$DB->escape($key)."','".$DB->escape($data)."');");
        }
        else
        {
            $data = $DB->query("SELECT data FROM states WHERE ID  = '".$DB->escape($key)."'")->fetch_array()["data"];
            $DB->query("DELETE FROM states WHERE ID = '".$DB->escape($key)."'");
        }

        return $data;
    }
    /** STATES **/

    /** ID_CARD **/
    function id_card($employee_id)
    {
        global $DB;
        global $URL;
        global $url;
        global $barcode;

        $data = $DB->query("SELECT * FROM employees WHERE ID = '".$DB->escape($employee_id)."'")->fetch_array();
        header("Content-Type: image/png");
        $im = imagecreatetruecolor(340, 188);
        $background_color = imagecolorallocate($im, 255, 255, 255);
        $border_color = imagecolorallocate($im, 0, 107, 179);
        $text_color = imagecolorallocate($im, 0, 0, 0);

        imagefill ($im, 0, 0, $background_color);

        /** BARCODE **/
        $_barcode = $barcode->code39_int($data["barcode"], 40, 1);
        imagecopyresampled ($im, $_barcode,10,150,0,0,ImageSX($_barcode),ImageSY($_barcode), ImageSX($_barcode), ImageSY($_barcode));
        /** BARCODE **/

        /** PHOTO **/
        $thumb = $data["photo"];
        $str = "data:image/jpeg;base64,";
        $thumb = base64_decode(substr($thumb,strlen($str)));
        $photo = imagecreatefromstring($thumb);
        
        imagecopyresampled ($im, $photo,ImageSX($im) - 100,4,0,0,ImageSX($photo),ImageSY($photo), ImageSX($photo), ImageSY($photo));
        /** PHOTO **/

        /** LOGO **/
        $thumb = make_thumb(file_get_contents("logo.jpg"),200,50);
        $str = "data:image/jpeg;base64,";
        $thumb = base64_decode(substr($thumb,strlen($str)));
        $logo = imagecreatefromstring($thumb);
        
        imagecopyresampled ($im, $logo,10,10,0,0,ImageSX($logo),ImageSY($logo), ImageSX($logo), ImageSY($logo));
        /** LOGO **/


        /** DRAW_BORDER **/
        $x = ImageSX($im);
        $y = ImageSY($im);
        for($i = 0; $i < 2; $i++)
        {
            ImageRectangle($im, $i - 1, $i - 1, $x--, $y--, $border_color);
        }
        /** DRAW_BORDER **/
        //                  
        header("Cache-Control: max-age=86400");        
        imagettftext($im, 16, 0, 10, 140, $text_color, "fonts/monof55.ttf", $data["name"]);
//        imagestring($im, 128, 20, 20, "ĐäVieS", $text_color);
        imagepng($im);
        imagedestroy($im);  
        die();      
    }
    /** ID_CARD **/

    /** MAKE_THUMB **/
    function make_thumb($src, $desired_width,$desired_height) 
    {
        /* read the source image */
        $source_image = imagecreatefromstring($src);
        $width = imagesx($source_image);
        $height = imagesy($source_image);
        
        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $_desired_height = floor($height * ($desired_width / $width));
        if($_desired_height > $desired_height * 2)
        {
            $_desired_height = $desired_height *2;
            $desired_width = floor($width * ($desired_height / $height)) * 2;
        }
        
        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $_desired_height);
        
        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $_desired_height, $width, $height);
        
        ob_start();
        imagejpeg($virtual_image);
        $ret = ob_get_clean();
        if(!$ret)
        {
            return false;
        }
        return 'data:image/jpeg;base64,' . base64_encode($ret);
    }
    /** MAKE_THUMB **/


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
    /** generate_timezone_list **/
    function generate_timezone_list()
    {
        static $regions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        );

        $timezones = array();
        foreach( $regions as $region )
        {
            $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
        }

        $timezone_offsets = array();
        foreach( $timezones as $timezone )
        {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
        }

        // sort timezone by offset
        asort($timezone_offsets);

        $timezone_list = array();
        foreach( $timezone_offsets as $timezone => $offset )
        {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );

            $pretty_offset = "UTC${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }

        return $timezone_list;
    }
    /** generate_timezone_list **/

    /** dateDiff **/
    function dateDiff($time1, $time2, $precision = 6,$min = false) 
    {
        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
          $ttime = $time1;
          $time1 = $time2;
          $time2 = $ttime;
        }
     
        // Set up intervals and diffs arrays
        $intervals = array('year','month','day','hour','minute','second');
        $diffs = array();
     
        // Loop thru all intervals
        foreach ($intervals as $interval) {
          // Create temp time from time1 and interval
          $ttime = strtotime('+1 ' . $interval, $time1);

          // Set initial values
          $add = 1;
          $looped = 0;
          // Loop until temp time is smaller than time2
          while ($time2 >= $ttime) {
            // Create new temp time from time1 and interval
            $add++;
            $ttime = strtotime("+" . $add . " " . $interval, $time1);
            $looped++;
          }
     
          $time1 = strtotime("+" . $looped . " " . $interval, $time1);
          $diffs[$interval] = $looped;
        }
        
        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
          // Break if we have needed precission
          if ($count >= $precision) {
            break;
          }
          // Add value and interval 
          // if value is bigger than 0
          if ($value > 0) {
            // Add s if value is not 1
            if ($value != 1) {
              $interval .= "s";
            }
            // Add value and interval to times array
            $times[] = $value . " " . $interval;
            $count++;
          }
        }
     
        // Return string with times
        return implode(", ", $times);
    }
    /** dateDiff **/

?>