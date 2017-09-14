<?php
	class auth_simple
	{
		var $REG;
		var $user;
		var $SESSION;
		var $O_SESSION;
		var $CUID;
		var $update_session;

		/** CONSTRUCTOR **/
		function __construct($__REG)
		{
			$this->update_session = false;
			$this->REG = $__REG;

			if($_COOKIE["CUID"] == NULL)
			{
				$this->CUID = md5($this->ip().mt_rand(0,9999999).time());
				setcookie("CUID", $this->CUID, time() + 60 * 60 * 24 * 365,"/");
			}
			else
			{
				$this->CUID = $_COOKIE["CUID"];
			}

			/** GET_SESSION **/
			if($this->SESSION == NULL && $this->CUID != NULL)
			{
				$tmp = $this->REG->query("AUTH_SESSIONS",$this->CUID);

				if(!is_array($tmp))
				{
					$tmp = array();
				}

				$this->SESSION = $tmp;

			}
			/** GET_SESSION **/
			$this->OSESSION = $this->SESSION;
		}
		/** CONSTRUCTOR **/

		/** DESTRUCTOR **/
		function __destruct() 
		{
			global $config;
			$osession = md5(serialize($this->OSESSION));
			$session = md5(serialize($this->SESSION));
			if($osession != $session)
			{
			    $ts = time() + $config["session_timeout"];
				$this->REG->query("AUTH_SESSIONS",$this->CUID,$this->SESSION,$ts);
			}
		}
		/** DESTRUCTOR **/

		/** IP **/
		function ip()
		{
			return $_SERVER["REMOTE_ADDR"];
		}
		/** IP **/

		/** LOGOUT **/
		function logout()
		{
			global $url;

			$this->SESSION["back_menu"] = "";
			$this->SESSION["user_session"] = "";
			
			$url->go(array());
		}
		/** LOGOUT **/

		/** CHECK **/
		function check()
		{
			global $DB;
			global $config;

			if(isset($_GET["token"]) && md5($config["admin_password"].$this->ip()) == $_GET["token"])
			{
				if($_GET["back_url"] && $_GET["back_menu"])
				{
					$tmp["url"] = $_GET["back_url"];
					$tmp["name"] = $_GET["back_menu"];					
					$this->SESSION["back_menu"];
				}
				return $this->login($config["admin_password"]);
			}
			
			if(!$this->SESSION["user_session"])
			{
				return false;
			}

			$user = $this->SESSION["user_session"];

			if($user["ID"])
			{
				$this->user = $user;
				return true;
			}
			else
			{
				return false;
			}
		}
		/** CHECK **/

		/** LOGIN **/
		function login($passwd)
		{
			global $config;

			if($this->SESSION["last_try"] + 3 > time())
			{
				return false;
			}
			else
			{
				if($config["admin_password"] == $passwd)
				{
					$user = array();
					$user["ID"] = 1;
					$user["name"] = "Administrator";
					$this->SESSION["user_session"] = $user;
					return true;
				}
				else
				{
					$this->SESSION["last_try"] = time();
					return false;
				}
			}
		}
		/** LOGIN **/

		/** UID **/
		function uid()
		{
			if(is_array($this->user))
			{
				return $this->user["ID"];
			}
			else
			{
				return "0";
			}
		}
		/** UID **/

		/** CUID **/
		function cuid()
		{
			return $this->CUID;
		}
		/** CUID **/

	};
?>