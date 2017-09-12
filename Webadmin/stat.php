<?php


	function generate_stat($EID,$row,$delta,$action = NULL)
	{
		global $DB;
		global $auth;

		$kiosk_uid = $auth->SESSION["kiosk_user"]["ID"];

		$stat = $DB->query("SELECT * FROM employee_stat WHERE EID = '".$DB->escape($kiosk_uid)."' AND closed = '0'")->fetch_array();
		if(!$stat["ID"])
		{
			$DB->query("INSERT INTO employee_stat (EID,created,updated,closed) VALUES ('".$DB->escape($kiosk_uid)."','".time()."','".time()."','0');");

			$stat = $DB->query("SELECT * FROM employee_stat WHERE EID = '".$DB->escape($kiosk_uid)."' AND closed = '0'")->fetch_array();
			if(!$stat["ID"])
			{
				echo("error: employee_stat");
				die();
			}
		}

		$stat_arr = unserialize($stat["data"]);
		if(!$stat_arr)
		{
			$stat_arr = array();
		}

		if(!is_array($row))
		{
			if($row == "close")
			{
				$ts = time();
				$arr = $stat_arr;
				$arr[$ts][$row] = $delta;
				$DB->query("UPDATE employee_stat SET data = '".$DB->escape(serialize($arr))."', updated = '".time()."', closed = '".time()."' WHERE ID = '".$DB->escape($stat["ID"])."'");
			}
			else
			{
				$ts = time();
				$arr = $stat_arr;
				$arr[$ts][$row] = $delta;
				$DB->query("UPDATE employee_stat SET data = '".$DB->escape(serialize($arr))."', updated = '".time()."' WHERE ID = '".$DB->escape($stat["ID"])."'");				
			}
		}
		else
		{
			$time = $delta["time"];
			$product = $DB->query("SELECT * FROM products WHERE item_number = '".$DB->escape($row["item_number"])."'")->fetch_array();
			$stages = unserialize($product["stages"]);

			$_stages = array();
			while(list($k,$v) = each($stages))
			{
				if($v["action"] > 0)
				{
					$_stages[$v["action"]] = $v["time"];
				}
			}


			$num = $delta["success"] + $delta["failed"];
			$optimal_time = $_stages[$action] * $num;
			$report_time = $time;

			$ts = time();
			$arr = $stat_arr;
			$arr2 = array();
			$arr2["PID"] = $row["ID"];
			$arr2["item_number"] = $row["item_number"];
			$arr2["optimal_time"] = $optimal_time;
			$arr2["report_time"] = $report_time;
			$arr2["action"] = $action;
			$arr2["success"] = $delta["success"];
			$arr2["failed"] = $delta["failed"];

			$arr[$ts]["process"] = $arr2;
			$DB->query("UPDATE employee_stat SET data = '".$DB->escape(serialize($arr))."', updated = '".time()."' WHERE ID = '".$DB->escape($stat["ID"])."'");
		}
	}
?>
