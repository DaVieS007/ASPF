<?php
	class _url
	{
		function get()
		{
			$ret = array();
			$data = $_SERVER['REQUEST_URI'];
			$data = explode("/",$data);
			while(list($k,$v) = each($data))
			{
				if(trim($v) != "")
				{
					if(strstr($v,"?"))
					{
						$v = explode("?",$v)[0];
					}
					$ret[] = urldecode($v);
				}
			}
			return $ret;
		}

		function write($arr)
		{
			$ret = "";
			if(!isset($_SERVER["HTTPS"]))
			{
				if(count($arr))
				{
					if($arr[0] == "!CLANG")
					{
						$arr[0] = $this->get()[0];
					}
					$ret = "http://".$_SERVER["HTTP_HOST"]."/".implode("/",$arr);
				}
				else
				{
					$ret = "http://".$_SERVER["HTTP_HOST"]."/";
				}
				
			}
			else
			{
				if(count($arr))
				{
					if($arr[0] == "!CLANG")
					{
						$arr[0] = $this->get()[0];
					}
					$ret = "https://".$_SERVER["HTTP_HOST"]."/".implode("/",$arr);
				}
				else
				{
					$ret = "https://".$_SERVER["HTTP_HOST"]."/";
				}
				
			}
			return $ret;
		}

		function go($arr)
		{
			header("Location: ".$this->write($arr));
			die();
		}
	}


?>