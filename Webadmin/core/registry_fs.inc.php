<?php
	class registry_fs
	{
		var $DB;
		var $CWD;

		/** CONSTRUCTOR **/
		function __construct($file)
		{
			$this->CWD = getcwd();
			$this->DB = $this->CWD."/".$file;
			if(!is_file($file))
			{
				file_put_contents($file,gzencode(serialize(array())));
			}
		}
		/** CONSTRUCTOR **/

		/** QUERY **/
		function query($GID,$key,$value = NULL,$expire=0)
		{
			if($value == NULL)
			{
				$fp = fopen($this->DB, "r+");
				if (flock($fp, LOCK_EX)) 
				{
					$tmp = file_get_contents($this->DB);
					$tmp = unserialize(gzdecode($tmp));
					flock($fp, LOCK_UN);
					fclose($fp);
				}
				else
				{
					fclose($fp);
					return false;
				}
				return $tmp[$GID][$key]["value"];
			}
			else
			{
				$fp = fopen($this->DB, "r+");
				if (flock($fp, LOCK_EX)) 
				{
					$tmp = file_get_contents($this->DB);
					$tmp = unserialize(gzdecode($tmp));
					$tmp[$GID][$key]["value"] = $value;
					$tmp[$GID][$key]["expire"] = $expire;
					file_put_contents($this->DB,gzencode(serialize($tmp)));
	
					flock($fp, LOCK_UN);
					fclose($fp);
				}
				else 
				{
					fclose($fp);
					return false;
				}				
				return true;
			}
		}
		/** QUERY **/

		/** CLEANUP **/
		function cleanup()
		{
			$fp = fopen($this->DB, "r+");
			if (flock($fp, LOCK_EX)) 
			{
				$tmp = file_get_contents($this->DB);
				$tmp = unserialize(gzdecode($tmp));
				$ntmp = array();
				while(list($GID,$arr) = each($tmp))
				{
					while(list($key,$arr2) = each($arr))
					{
						if($arr2["expire"] > 0 && $arr2["expire"] < time())
						{
							/* SKIP */
						}
						else
						{
							$ntmp[$GID][$key] = $arr2;
						}
					}
				}
				file_put_contents($this->DB,gzencode(serialize($ntmp)));
				flock($fp, LOCK_UN);
				fclose($fp);
			}
			else 
			{
				fclose($fp);
				return false;
			}				
			return true;		
		}
		/** CLEANUP **/
	}

?>