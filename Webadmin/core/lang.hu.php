<?php

$L = array();

$L["lang_prefix"] = "hu";
$L["prefix"] = "hu";

$L["edit"] = "Szerkesztés";
$L["welcome"] = "Üdvözöljük a ";
$L["product"] = "ASPF";
$L["product2"] = "rendszerében!";

$L["mitigation_level"] = "SPAM Enyhítési Szint";
$L["mitigation_0"] = "Ne legyen szűrés";
$L["mitigation_1"] = "Szint 1: Jelöld SPAMnek: Hoszt név és a DNS nem teljes";
$L["mitigation_2"] = "Szint 2: Jelöld SPAMnek: PTR / Reverse nem egyenlő a HELO/EHLO névvel";
$L["mitigation_3"] = "Szint 3: Jelöld SPAMnek: Küldő SMTP szervere nem képes kapcsolatokat fogadni";
$L["mitigation_4"] = "Szint 4: RBL Lookup";
$L["mitigation_5"] = "Szint 5: Jelöld SPAMnek: noreply vagy bounce szerepel a feladóban";

$L["last_limited"] = "Limitált Küldések";
$L["whitelist_help"] = "Fehérlistázod levelek és domainek egyből ellesznek fogadva, azonban a kifelé küldés limitekre nincs hatással.";
$L["blacklist_help"] = "Feketelistázott levelek és domainek egyből SPAMként értelmeződnek, illetve ezekről a címekről kifelé sem enged küldeni.";

$L["rbl_list"] = "RBL Lista";
$L["drop_mail_instead_of_mark"] = "Dobja el a levelet, jelölés helyett (nincs SPAM mappa)";
$L["gray_learn_recip_domain"] = "Minden távoli címzett domainja fehérlistára vétele";
$L["gray_learn_recip"] = "Minden távoli címzett fehérlistára vétele";
$L["gray_learn_expire"] = "Auto-Learn Lejárata (nap)";
$L["gray_cache_expire"] = "Gyorsítótár Lejárata (nap)";
$L["limit_mails_per_user"] = "Levélküldési Ráta felhasználóként / 5perc";
$L["enable_limit_reject"] = "Küldés megtiltása ha túllépte a kvótát";
$L["notify_command"] = "Külső alkalmazás indítása ha egy felhasználó túllépte a kvótáját";

$L["logout"] = "Kijelentkezés";
$L["title"] = "ASPF";

$L["dashboard"] = "Műszerfal";
$L["dashboard_desc"] = "Üdvözöljük, {!USER:NAME}!";

$L["enter_password"] = "Adja meg a jelszavát";
$L["login_button"] = "Biztonságos Bejelentkezés";
$L["login_desc"] = "Kérjük <span>jelentkezzen be</span> a rendszer használatához!<br /><strong>Rendszerünk védett a támadásokkal szemben.</strong>";

$L["login_failed"] = "A belépés sikertelen, nem megfelelő adatok!";
$L["form_submit"] = "Változások Mentése";
$L["settings"] = "Beállítások";
$L["aspf_never_run"] = "ASPF még nem futott soha ...";
$L["aspf_offline"] = "ASPF Jelenleg Inaktív!";
$L["aspf_online"] = "ASPF Jelenleg Aktív";
$L["current_usage"] = "Jelenlegi Kihasználtság";
$L["history"] = "óra történései";
$L["mail_passed"] = "Levél ment keresztül a rendszeren";

$L["mail_sent"] = "Elküldött Levél (Normális)";
$L["mail_reject_to_send"] = "Visszautasított küldött levél (Kifelé SPAMMELÉS)";
$L["mail_accept"] = "Levél Elfogadva (Normális)";
$L["mail_caught"] = "Levél SPAM -nek jelölve";

$L["node_name"] = "Levelező Szerver";
$L["node_last_seen"] = "Utolsó Aktivitás";

$L["sender"] = "Feladó";
$L["count"] = "Darabszám";
$L["outgoing_sending"] = "Kifelé Küld";
$L["incoming_traffic"] = "Bejövő Forgalom";

$L["last_negative"] = "Negatív Eredmények";

$L["date"] = "Dátum";
$L["investigate"] = "Kivizsgálás";

$L["passthrough"] = "Átengedve";
$L["blacklisted"] = "Kitiltva";
$L["whitelisted"] = "Fehérlistás";
$L["cached"] = "Gyorsítótárazva";
$L["unknown"] = "Nem ismert";
$L["limited"] = "Limitálva";
$L["rejected"] = "Elutasítva";
$L["dunno"] = "Megjelölve SPAM -nek";
$L["accepted"] = "Elfogadva";
$L["sender_domain"] = "Küldő Domain";
$L["recipient_domain"] = "Címzett Domain";
$L["whitelist"] = "Fehérlista";
$L["blacklist"] = "Feketelista";
$L["whitelist_senders"] = "Küldő Fehérlista";
$L["whitelist_domains"] = "Domain Fehérlista";
$L["blacklist_senders"] = "Küldő Feketelista";
$L["blacklist_domains"] = "Domain Feketelista";

$L["remove"] = "Eltávolítás";

$L["expire"] = "Lejárat"; 
$L["sender_or_domain"] = "Küldő Email vagy Domain";
$L["add_to_list"] = "Listához Adás";
$L["1day"] = "1 Nap";
$L["1week"] = "1 Hét";
$L["1month"] = "1 Hónap";
$L["1year"] = "1 Év";
$L["10year"] = "10 Év";

$L["add_sender_to_blacklist"] = "Küldő feketelistára küldése (1 évre)";
$L["add_sender_domain_to_blacklist"] = "Küldő Domain feketelistára küldése (1 évre)";

$L["add_sender_to_whitelist"] = "Küldő fehérlistázása (1 évre)";
$L["add_sender_domain_to_whitelist"] = "Küldő Domain fehérlistázása (1 évre)";

$L["remove_sender"] = "Küldő alaphelyzetbe állítása";
$L["remove_sender_domain"] = "Küldő Domain alaphelyzetbe álíltása";

$L["outgoing"] = "Kimenő";
$L["incoming"] = "Bejövő";

$L["results"] = "Találatok a következőre";
$L["recipient"] = "Címzett";
$L["smtp_name"] = "SMTP Név";
$L["sender_name"] = "Küldő Peer Név";
$L["until"] = "Eddig";

$L["dt_empty"] = "Nincs megjeleníthető adat";
$L["dt_sinfo"] = "Mutatom a _START_ - _END_ -ig _TOTAL_ bejegyzésből";
$L["dt_sinfo_empty"] = "Mutatom a 0 - 0 -ig 0 bejegyzésből";
$L["dt_filtered"] = "Filterezve a(z) _MAX_ bejegyzésből";
$L["dt_entries"] = "Mutat _MENU_ bejegyzés";
$L["dt_no_records"] = "Nincs egyező találat";
$L["mail"] = "E-Mail";
$L["passwd"] = "Jelszó";
$L["lostpw"] = "Elfelejtett Jelszó";
$L["loading"] = "Betöltés";
$L["search"] = "Keresés";
$L["previous"] = "Elöző";
$L["next"] = "Következő";
$L["last"] = "Utolsó";
$L["first"] = "Első";
$L["yes"] = "Igen";
$L["no"] = "Nem";
$L["cancel"] = "Mégse";
$L["create"] = "Létrehozás";
$L["save"] = "Mentés";
$L["delete"] = "Törlés";
$L["delete_desc"] = "Ha egyszer törölve lett, már nem lehet visszaállítani";
$L["latency"] = "Válaszidő";
$L["ms"] = "Milliszekundom";
$L["last_online"] = "Utoljára Aktív";
$L["capability"] = "Módszerek";
$L["details"] = "Részletek";
$L["state"] = "Állapot";
$L["charts"] = "Grafikonok";
$L["page"] = "Oldal";
$L["changes_saved"] = "A változások elmentve!";


?>