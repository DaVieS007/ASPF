<?php

$L = array();

$L["lang_prefix"] = "en";
$L["prefix"] = "en";

$L["edit"] = "Edit";
$L["welcome"] = "Welcome to ";
$L["product"] = "ASPF";
$L["product2"] = "system!";

$L["mitigation_level"] = "SPAM Mitigation Level";
$L["mitigation_0"] = "Disable SPAM Detect";
$L["mitigation_1"] = "Level 1: Mark as SPAM when: Hostname and DNS are incomplete";
$L["mitigation_2"] = "Level 2: Mark as SPAM when: Reverse Domain is not equal with HELO/EHLO";
$L["mitigation_3"] = "Level 3: Mark as SPAM when: When sender SMTP Server (MX) is not open for incoming messages";
$L["mitigation_4"] = "Level 4: RBL Lookup";
$L["mitigation_5"] = "Level 5: Mark as SPAM when: Contains noreply or bounce";

$L["last_limited"] = "Limited Mails";
$L["whitelist_help"] = "Whitelisted mails or domains immediately accepted, but outgoing limits will be applied independently.";
$L["blacklist_help"] = "Blacklisted mails or domains immediately marks as SPAM and outgoing sendings from these address is prohibited too.";
$L["sending_limit"] = "Mail sending limit / 5 minutes";
$L["limit"] = "Limit";
$L["mail_limit"] = "Mail Limit";
$L["domain_limit"] = "Domain Limit";
$L["sender_domain"] = "Sender Domain";

$L["custom_levels"] = "Custom Levels";
$L["clevel_help"] = "Custom SPAM filtering settings domain or address based";
$L["clevel_mails"] = "Mail Based Custom Filtering Levels";
$L["clevel_domains"] = "Domain Based Custom Filtering Levels";
$L["clevel"] = "Filtering Level";
$L["domain"] = "Domain";

$L["limits"] = "Sending Limits";

$L["404_not_found"] = "404 The page that you requested is not found";
$L["404_not_found_desc"] = "Try navigate to another page";



$L["rbl_list"] = "RBL List";
$L["drop_mail_instead_of_mark"] = "Drop Mail instead of mark as SPAM (No SPAM Folder)";
$L["gray_learn_recip_domain"] = "Add all remote recipients's domain to whitelist";
$L["gray_learn_recip"] = "Add all remote recipients to whitelist";
$L["gray_learn_expire"] = "Auto-Learn Expire (day)";
$L["gray_cache_expire"] = "Cache Expire (day)";
$L["limit_mails_per_user"] = "Limiting Outgoing Sending per user / 5 minutes";
$L["enable_limit_reject"] = "Reject sending when user limit his rate";
$L["notify_command"] = "Enter mail or URL or command to execute when outgoing spamming";

$L["logout"] = "Log-Out";
$L["title"] = "ASPF";

$L["dashboard"] = "Dashboard";
$L["dashboard_desc"] = "Welcome, {!USER:NAME}!";

$L["enter_password"] = "Type your password";
$L["login_button"] = "Secure Log In";
$L["login_desc"] = "Please <span>login</span> to use this system!";
$L["login_desc2"] = "Type your Password!";

$L["login_failed"] = "Invalid Credentials!";
$L["form_submit"] = "Change Saves";
$L["settings"] = "Settings";
$L["aspf_never_run"] = "ASPF Never Run yet..";
$L["aspf_offline"] = "ASPF Currently Offline!";
$L["aspf_online"] = "ASPF Currently Online!";
$L["current_usage"] = "Current Usage";
$L["history"] = "hours of history";
$L["mail_passed"] = "Messages passed through the system.";

$L["mail_sent"] = "Mail Sent (Normally)";
$L["mail_reject_to_send"] = "Mail Rejected to Send (Outgoing Spamming)";
$L["mail_accept"] = "Mail Accepted (Normally)";
$L["mail_caught"] = "Mail Detected as SPAM";

$L["node_name"] = "Mailing Server";
$L["node_last_seen"] = "Last Activity";

$L["sender"] = "Sender";
$L["count"] = "Count";
$L["outgoing_sending"] = "Sending Outgoing";
$L["incoming_traffic"] = "Incoming Traffic";

$L["last_negative"] = "Negative Summary";

$L["date"] = "Date";
$L["investigate"] = "Investigate";

$L["passthrough"] = "Passthrough";
$L["blacklisted"] = "BlackListed";
$L["whitelisted"] = "Whitelisted";
$L["unknown"] = "Unknown";
$L["cached"] = "Cached";
$L["limited"] = "Limited";
$L["rejected"] = "Rejected";
$L["dunno"] = "Marked as SPAM";
$L["accepted"] = "Accepted";
$L["sender_domain"] = "Sender's Domain";
$L["recipient_domain"] = "Recipient's Domain";
$L["whitelist"] = "WhiteList";
$L["blacklist"] = "BlackList";
$L["whitelist_senders"] = "Sender's Whitelist";
$L["whitelist_domains"] = "Domain Whitelist";
$L["blacklist_senders"] = "Sender's Blacklist";
$L["blacklist_domains"] = "Domain Blacklist";
$L["remove"] = "Remove";

$L["expire"] = "Expiration"; 
$L["sender_or_domain"] = "Sender or sender's domain";
$L["add_to_list"] = "Add to the List";
$L["1day"] = "1 Day";
$L["1week"] = "1 Week";
$L["1month"] = "1 Month";
$L["1year"] = "1 Year";
$L["10year"] = "10 Years";

$L["add_sender_to_blacklist"] = "Add Sender to Blacklist (1 year)";
$L["add_sender_domain_to_blacklist"] = "Add Sender's Domain to Blacklist (1 year)";

$L["add_sender_to_whitelist"] = "Add Sender to Whitelist (1 year)";
$L["add_sender_domain_to_whitelist"] = "Add Sender's Domain to Whitelist (1 year)";

$L["remove_sender"] = "Clear sender status";
$L["remove_sender_domain"] = "Clear Sender domain status";


$L["outgoing"] = "Outgoing";
$L["incoming"] = "Incoming";

$L["results"] = "Results for";
$L["recipient"] = "Recipient";
$L["smtp_name"] = "SMTP Name";
$L["sender_name"] = "Sender Peer Name";
$L["until"] = "Until";

$L["dt_empty"] = "No viewable datas";
$L["dt_sinfo"] = "Showing from _START_ to _END_ from total _TOTAL_ rows";
$L["dt_sinfo_empty"] = "Nothing to show";
$L["dt_filtered"] = "Filtered from _MAX_ rows";
$L["dt_entries"] = "Show _MENU_ Rows";
$L["dt_no_records"] = "There is nothing to matches";
$L["mail"] = "E-Mail";
$L["passwd"] = "Password";
$L["lostpw"] = "Forgotten Password";
$L["loading"] = "Loadin";
$L["search"] = "Search";
$L["previous"] = "Previous";
$L["next"] = "Next";
$L["last"] = "Last";
$L["first"] = "First";
$L["yes"] = "Yes";
$L["no"] = "No";
$L["cancel"] = "Cancel";
$L["create"] = "Create";
$L["save"] = "Save";
$L["delete"] = "Delete";
$L["delete_desc"] = "If once delete cannot be restored";
$L["latency"] = "Latency";
$L["ms"] = "Millisecoundum";
$L["last_online"] = "Last Active";
$L["capability"] = "Capability";
$L["details"] = "Deatils";
$L["state"] = "State";
$L["charts"] = "Charts";
$L["page"] = "Page";
$L["changes_saved"] = "Changes Saved!";


?>