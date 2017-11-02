<?php
	$widget->head(12,L("DASHBOARD"));
	$CONTENT = $widget->row();

	/** NODE EDIT **/
	if($URL[2])
	{
		$data = explode(":",$URL[2]);
		if($data[0] == "node")
		{

			$settings = $DB->query("SELECT * FROM nodes WHERE ID = '".$DB->escape($data[1])."'")->fetch_array();
			if(!$settings["ID"])
			{
				unset($URL[2]);
				$url->go($URL);
			}
			$config = json_decode($settings["settings"],true);
			
			if(count($_POST))
			{
				if($_POST["delete"] == "1")
				{
					$DB->query("DELETE FROM nodes WHERE ID = '".$settings["ID"]."'");
					unset($URL[2]);
					$url->go($URL);
				}
				else
				{
					while(list($k,$v) = each($_POST))
					{
						$key = explode("|",$k);
						if($k == "SPAM_DETECT|rbl_list")
						{
							$v = str_replace("\n",",",$v);
							$v = str_replace(" ","",$v);
							$v = str_replace(",,",",",$v);
						}

						$config[$key[0]][$key[1]] = $v;
					}
					$DB->query("UPDATE nodes SET settings = '".$DB->escape(json_encode($config))."' WHERE ID = '".$settings["ID"]."'");
					$widget->form_note("success",$widget->bold(L("CHANGES_SAVED")));	
				}
			}

			$sel = array();
			$sel["0"] = L("NO");
			$sel["1"] = L("YES");

			$mitigation_level = array();
			$mitigation_level["0"] = L("MITIGATION_0");
			$mitigation_level["1"] = L("MITIGATION_1");
			$mitigation_level["2"] = L("MITIGATION_2");
			$mitigation_level["3"] = L("MITIGATION_3");
			$mitigation_level["4"] = L("MITIGATION_4");
			$mitigation_level["5"] = L("MITIGATION_5");
			
			

			$widget->form_select("SPAM_DETECT|spam_mitigation_level",L("MITIGATION_LEVEL"),"",$mitigation_level,$config["SPAM_DETECT"]["spam_mitigation_level"],2);
			$widget->form_select("SPAM_DETECT|drop_mail_instead_of_mark_spam",L("DROP_MAIL_INSTEAD_OF_MARK"),"",$sel,$config["SPAM_DETECT"]["drop_mail_instead_of_mark_spam"],2);
			$widget->form_sep();
			$widget->form_text("SPAM_DETECT|rbl_list",L("RBL_LIST"),"",str_replace(",","\n",$config["SPAM_DETECT"]["rbl_list"]));						
			$widget->form_sep();			
			$widget->form_select("GRAYLIST|gray_learn_recipient_domain",L("GRAY_LEARN_RECIP_DOMAIN"),"",$sel,$config["GRAYLIST"]["gray_learn_recipient_domain"],2);
			$widget->form_select("GRAYLIST|gray_learn_recipient_mail",L("GRAY_LEARN_RECIP"),"",$sel,$config["GRAYLIST"]["gray_learn_recipient_mail"],2);
			$widget->form_sep();			
			$widget->form_input("GRAYLIST|gray_learn_expire","text",L("GRAY_LEARN_EXPIRE"),"",$config["GRAYLIST"]["gray_learn_expire"],4);						
			$widget->form_input("GRAYLIST|gray_cache_expire","text",L("GRAY_CACHE_EXPIRE"),"",$config["GRAYLIST"]["gray_cache_expire"],4);						
			$widget->form_input("ANTISPAM|limit_mails_per_user","text",L("LIMIT_MAILS_PER_USER"),"",$config["ANTISPAM"]["limit_mails_per_user"],3);						
			$widget->form_select("ANTISPAM|enable_limit_reject",L("ENABLE_LIMIT_REJECT"),"",$sel,$config["ANTISPAM"]["enable_limit_reject"],4);
			$widget->form_sep();						
			$widget->form_input("ANTISPAM|notify_command","text",L("NOTIFY_COMMAND"),"",$config["ANTISPAM"]["notify_command"],2);						
			$widget->form_select("delete",L("DELETE"),"",$sel,0,4);
			$widget->form_sep();						
			
		
			

			$widget->form(12,"danger",$settings["name"],L("FORM_SUBMIT"),$url->write($curl));			
		}
	}

	/** NODE EDIT **/
	else
	{
		$state = $DB->query("SELECT * FROM state WHERE `key` = 'workers'")->fetch_array();
		if(!$state["key"])
		{
			$widget->infobar(12,"danger",L("ASPF_NEVER_RUN"));
		}
		else
		{
			
			if($state["tstamp"] + 120 < time())
			{
				$widget->infobar(12,"danger",L("ASPF_OFFLINE"));
			}
			else
			{
				$data = json_decode($state["data"],true);
				$percent = (100 / $data["workers"]["max"])*$data["workers"]["current"];
	
				$widget->infobar(12,"success",L("ASPF_ONLINE"));
				$A = $widget->row();
	
				$widget->lead(12,L("CURRENT_USAGE").": ".$data["workers"]["current"]." / ".$data["workers"]["max"]." ( ".$percent."% )");
				if($percent > 90)
				{
					$widget->add($widget->col(12,$widget->progress("danger",$percent)));							
				}
				else if($percent > 75)
				{
					$widget->add($widget->col(12,$widget->progress("warning",$percent)));							
				}
				else if($percent > 45)
				{
					$widget->add($widget->col(12,$widget->progress("primary",$percent)));											
				}
				else
				{
					$widget->add($widget->col(12,$widget->progress("success",$percent)));											
				}
	
				$B = $widget->row();
				$CONTENT .= $widget->col(6,$A);
				$CONTENT .= $widget->col(6,$B);
			}
		}
		$CONTENT .= $widget->row();
		
		$ts = time() - 3600*$config["show_latest"];
		$sent = $DB->query("SELECT COUNT(ID) AS CC FROM transactions WHERE action = 'sent' AND tstamp > '".$ts."'")->fetch_array()["CC"];
		$reject = $DB->query("SELECT COUNT(ID) AS CC FROM transactions WHERE (action = 'limit') AND tstamp > '".$ts."'")->fetch_array()["CC"];
	
		$accept = $DB->query("SELECT COUNT(ID) AS CC FROM transactions WHERE action = 'accept' AND tstamp > '".$ts."'")->fetch_array()["CC"];
		$dunno = $DB->query("SELECT COUNT(ID) AS CC FROM transactions WHERE (action = 'dunno' OR action = 'reject') AND tstamp > '".$ts."'")->fetch_array()["CC"];
	
		$total = $sent + $reject + $dunno + $accept;
		$tp = 100 / $total;
	
		$widget->head(12,$config["show_latest"]." ".L("HISTORY"));
		
		$CONTENT .= $widget->row();
		
		
		$sent_p = $tp * $sent;
		$reject_p = $tp * $reject;
		$accept_p = $tp * $accept;
		$dunno_p = $tp * $dunno;
	
		/** STAT **/
		$table = array();
		$table["th"] = array("","","");
		$table["td"][] = array(L("MAIL_SENT")."<br />".$widget->progress("primary",$sent_p),$sent,round($sent_p)."%");
		$table["td"][] = array(L("MAIL_REJECT_TO_SEND")."<br />".$widget->progress("warning",$reject_p),$reject,round($reject_p)."%");
		$table["td"][] = array(L("MAIL_ACCEPT")."<br />".$widget->progress("success",$accept_p),$accept,round($accept_p)."%");
		$table["td"][] = array(L("MAIL_CAUGHT")."<br />".$widget->progress("danger",$dunno_p),$dunno,round($dunno_p)."%");
		
		$widget->table(12,$total." ".L("MAIL_PASSED"),$table["th"],$table["td"]);
		/** STAT **/	
	
		/** NODES **/
		$table = array();
		$table["th"] = array(L("NODE_NAME"),L("NODE_LAST_SEEN"),"");
		$res = $DB->query("SELECT * FROM nodes;");
		while($row = $res->fetch_array())
		{
			$nurl = $URL;
			$nurl[2] = "node:".$row["ID"];
			$table["td"][] = array($row["name"],date($config["date_format"],$row["last_seen"]),$widget->button("danger",L("EDIT"),$url->write($nurl)));
			
		}
		
		$widget->table(12,"",$table["th"],$table["td"]);
		/** NODES **/	
	
		$A = $widget->row();
	
		/** LAST_LIMITED **/
		$table = array();
		$table["th"] = array(L("SENDER"),L("DATE"),"");
		$res = $DB->query("SELECT COUNT(ID) AS CC,ID,sender, action, tstamp FROM `transactions` WHERE tstamp > '".$ts."'  AND action = 'limit' GROUP BY sender ORDER BY `tstamp` DESC");
		while($row = $res->fetch_array())
		{
			$sender = mailb($row["sender"]);
			$sender .= " (".$row["CC"].")";
			if($row["action"] == "blacklist")
			{
				$sender .= "<br />".$widget->badge(L("OUTGOING"),"danger")."  ".$widget->badge(L("BLACKLISTED"),"danger");
			}
			else if($row["action"] == "limit")
			{
				$sender .= "<br />".$widget->badge(L("OUTGOING"),"danger")."  ".$widget->badge(L("LIMITED"),"primary");
			}
			else if($row["action"] == "reject")
			{
				$sender .= "<br />".$widget->badge(L("INCOMING"),"primary")."  ".$widget->badge(L("REJECTED"),"warning");
			}
			else if($row["action"] == "dunno")
			{
				$sender .= "<br />".$widget->badge(L("INCOMING"),"primary")."  ".$widget->badge(L("DUNNO"),"info");
			}
	
			$nurl = $URL;
			$nurl[1] = "search";
			$nurl[2] = urlencode($row["sender"]);
			$nurl[3] = $row["ID"];
			$table["td"][] = array($sender,date($config["date_format"],$row["tstamp"]),$widget->button("danger",L("INVESTIGATE"),$url->write($nurl)));
			
		}
	
		$widget->table(12,L("LAST_LIMITED"),$table["th"],$table["td"],"dt_negatives","1:desc");
		/** LAST_LIMITED **/
	
		$B = $widget->row();
	
		$widget->add($widget->col(6,$A));
		$widget->add($widget->col(6,$B));
		$CONTENT .= $widget->row();	
	
		/** TOP OUTGOING SPAM **/
		$table = array();
		$table["th"] = array(L("SENDER"),L("COUNT"),"");
		$res = $DB->query("SELECT COUNT(ID) AS CC, sender, action FROM `transactions` WHERE tstamp > '".$ts."' AND (action = 'sent' OR action = 'limit' OR action = 'blacklist') GROUP BY sender ORDER BY `CC` DESC LIMIT 0,50");
		while($row = $res->fetch_array())
		{
			$sender = mailb($row["sender"]);
			if($row["action"] == "blacklist")
			{
				$sender .= "<br />".$widget->badge(L("BLACKLISTED"),"danger");
			}
			else if($row["action"] == "limit")
			{
				$sender .= "<br />".$widget->badge(L("LIMITED"),"primary");
			}
			else
			{
				$sender .= "<br />".$widget->badge(L("PASSTHROUGH"),"success");
			}
	
			$nurl = $URL;
			$nurl[1] = "search";
			$nurl[2] = urlencode($row["sender"]);
			$table["td"][] = array($sender,$row["CC"],$widget->button("danger",L("INVESTIGATE"),$url->write($nurl)));
			
		}
	
		$widget->table(6,L("OUTGOING_SENDING"),$table["th"],$table["td"],"dt_spammers","1:desc");
		/** TOP OUTGOING SPAM **/
	
	
		/** TOP INCOMING SPAM **/
		$table = array();
		$table["th"] = array(L("SENDER"),L("COUNT"),"");
		$res = $DB->query("SELECT COUNT(ID) AS CC, sender, action FROM `transactions` WHERE tstamp > '".$ts."' AND (action = 'accept' OR action = 'dunno' OR action = 'reject') GROUP BY sender ORDER BY `CC` DESC LIMIT 0,50");
		while($row = $res->fetch_array())
		{
			$sender = mailb($row["sender"]);
			if($row["action"] == "reject")
			{
				$sender .= "<br />".$widget->badge(L("REJECTED"),"danger");
			}
			else if($row["action"] == "dunno")
			{
				$sender .= "<br />".$widget->badge(L("DUNNO"),"warning");
			}
			else
			{
				$sender .= "<br />".$widget->badge(L("ACCEPTED"),"success");
			}
	
			$nurl = $URL;
			$nurl[1] = "search";
			$nurl[2] = urlencode($row["sender"]);
			$table["td"][] = array($sender,$row["CC"],$widget->button("danger",L("INVESTIGATE"),$url->write($nurl)));
			
		}
	
		$widget->table(6,L("INCOMING_TRAFFIC"),$table["th"],$table["td"],"dt_income_spam","1:desc");
		/** TOP INCOMING SPAM **/
	}

	$CONTENT .= $widget->row();
?>