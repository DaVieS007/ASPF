# ASPF
## Advanced SPAM Processing Filter
### Part of MVCP Mass Virtualhost Control Panel (BSD)

##### Author
- Hlavaji Viktor / DaVieS (davies@npulse.net)

##### Credits
- Thanks for Systech Global Kft for testing and funding this project (https://systech.hu)

##### Screnshots
![ASPF Summary](/aspf-1.png?raw=true "ASPF Summary")
![ASPF Search](/aspf-2.png?raw=true "ASPF Search")

### Workflow
- Written in PHP, Compatible with PHP5 and above included PHP7.1
- The software listens on TCP 10099 (both INET4 and INET6)
- Analyze SMTP Fields then register action in mySQL database
- Only need to add content policy to inet:127.0.0.1:10099 or [::1]:10099 (on postfix)
- This software not processing any content of mails, just playing with SMTP fields
- There several SPAM filtering levels which can be adjusted on the config or from webadmin
- This Package includes webadmin
- Enterprise Class Centralized Statistics from mail sending and receiving operations
- Supports: Postfix, Exim
- Tested on Debian based Linux and FreeBSD: (For compat, you may need symlink /usr/local/bin/php -> /usr/bin/php)

### Features
- Statistics
- Whitelist address / domain Based
- Blacklist address / domain Based
- Sending Limits address / domain Based
- Graylist operation (Auto-Learn)
- Configurable RBL Lists / per server
- Configurable SPAM Detection algorythm / per server / address / domain


### How to Install
#### Linux:
- Extract this package in /opt init scripts are included
- Setup mySQL Database (included) and mySQL account
- Configure with **aspf.conf**
- Copy init script to /etc/init.d
- Then run: **update-rc.d aspf_linux defaults**
- Start the script: **service aspf_linux start**
- Create a PHP VirtualHost for ASPF Webadmin then log-in

#### FreeBSD:
- Extract this package in /opt init scripts are icluded
- Setup mySQL Database (included) and mySQL account
- Configure with **aspf.conf**
- Copy init script to /usr/local/etc/rc.d then rename it to aspf
- Add to rc.conf or rc.conf.d **aspf_enable="YES"**
- Start the script: **service aspf start**
- Create a PHP VirtualHost for ASPF Webadmin

### Postfix Configuration

#### [main.cf] file
````
smtpd_recipient_restrictions =
   check_policy_service inet:127.0.0.1:10099
   permit_mynetworks,
   permit_sasl_authenticated,
   reject_non_fqdn_hostname,
   reject_non_fqdn_sender,
   reject_non_fqdn_recipient,
   reject_unauth_destination,
   reject_unauth_pipelining,
   reject_invalid_hostname
````

#### [master.cf] file
````
127.0.0.1:10025 inet n - - - - smtpd
  -o content_filter=
  -o local_recipient_maps=
  -o relay_recipient_maps=
  -o smtpd_restriction_classes=
  -o smtpd_delay_reject=no
  -o smtpd_client_restrictions=permit_mynetworks,reject
  -o smtpd_helo_restrictions=
  -o smtpd_sender_restrictions=
  -o smtpd_recipient_restrictions=permit_mynetworks,reject
  -o smtpd_data_restrictions=reject_unauth_pipelining
  -o smtpd_end_of_data_restrictions=
  -o mynetworks=127.0.0.0/8
  -o smtpd_error_sleep_time=0
  -o smtpd_soft_error_limit=1001
  -o smtpd_hard_error_limit=1000
  -o smtpd_client_connection_count_limit=0
  -o smtpd_client_connection_rate_limit=0
  -o receive_override_options=no_header_body_checks,no_unknown_recipient_checks
````

#### Dovecot/Sieve FOLDER/DROP
````
# Move spam to spam folder
if header :contains "X-Spam-Flag" "YES" {
        fileinto "Junk";
        # Stop here so that we do not reply on spams
        stop;
}

if header :contains "X-ASPF" "!REJECT" {
        fileinto "Junk";
        # Stop here so that we do not reply on spams
        stop;
}
````


### Exim Configuration
````
acl_check_message:
# ASPF
deny log_message = greylisted host $sender_host_address
       set acl_m0  = request=smtpd_access_policy\nexim_auth=$sender_host_authenticated\ncompatibility=exim\nprotocol_state=RCPT\nprotocol_name=${uc:$received_protocol}\nhelo_name=$sender_helo_name\nclient_address=$sender_host_address\nclient_name=$sender_host_name\nsender=$sender_address\nrecipient=$recipients\n\n
       set acl_m0  = ${sg{${readsocket{inet:domain_or_ip_here:10099}{$acl_m0}{30s}{}{action=DUNNO}}}{action=}{}}
       message     = ${sg{$acl_m0}{^\\w+\\s*}{}}
       condition   = ${if eq{${uc:${substr{0}{6}{$acl_m0}}}}{REJECT}{true}{false}}

warn message     = ${sg{$acl_m0}{^\\w+\\s*}{}}
       condition   = ${if eq{${uc:${substr{0}{7}{$acl_m0}}}}{PREPEND}{true}{false}}
````

#### SPAM FOLDER/DROP
````
maildir_spam:
 driver = accept
 local_parts = !www:!root:!nobody:!postmaster:!abuse:!admin
 transport = virtual_localdelivery_spam
 condition = ${if match {$h_X-ASPF:} {!REJECT}}
````
