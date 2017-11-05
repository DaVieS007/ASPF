SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `archive_stat` (
  `ID` int(255) UNSIGNED NOT NULL,
  `local_mail` varchar(255) NOT NULL,
  `in` int(10) UNSIGNED NOT NULL,
  `out` int(10) UNSIGNED NOT NULL,
  `tstamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `custom_level` (
  `ID` int(10) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `level` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `domains` (
  `ID` int(10) UNSIGNED NOT NULL,
  `domain` varchar(255) NOT NULL,
  `type` enum('whitelist','blacklist','pending','auto-whitelist') NOT NULL,
  `expire` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `live_stat` (
  `ID` int(255) UNSIGNED NOT NULL,
  `local_mail` varchar(255) NOT NULL,
  `in` int(10) UNSIGNED NOT NULL,
  `out` int(10) UNSIGNED NOT NULL,
  `first` int(10) UNSIGNED NOT NULL,
  `last` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mail_limit` (
  `ID` int(10) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `limit` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `nodes` (
  `ID` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip4` varchar(255) NOT NULL,
  `ip6` varchar(255) NOT NULL,
  `settings` text NOT NULL,
  `last_seen` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `senders` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `smtp_ip` varchar(255) NOT NULL,
  `smtp_name` varchar(255) NOT NULL,
  `sender_ip` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `type` enum('whitelist','blacklist','cache','auto-whitelist') NOT NULL,
  `expire` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `state` (
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `data` blob NOT NULL,
  `tstamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE `transactions` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `smtp_ip` varchar(255) NOT NULL,
  `smtp_name` varchar(255) NOT NULL,
  `sender_ip` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `message2` text NOT NULL,
  `tstamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `archive_stat`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `local_mail` (`local_mail`),
  ADD KEY `tstamp` (`tstamp`);

ALTER TABLE `custom_level`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `domain` (`domain`),
  ADD KEY `address` (`address`) USING BTREE;

ALTER TABLE `domains`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD KEY `expire` (`expire`);

ALTER TABLE `live_stat`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `local_mail` (`local_mail`),
  ADD KEY `tstamp` (`last`),
  ADD KEY `first` (`first`);

ALTER TABLE `mail_limit`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `address` (`address`),
  ADD KEY `domain` (`domain`);

ALTER TABLE `nodes`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `senders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `sender` (`address`),
  ADD KEY `type` (`type`),
  ADD KEY `expire` (`expire`),
  ADD KEY `smtp_ip` (`smtp_ip`);

ALTER TABLE `state`
  ADD PRIMARY KEY (`key`),
  ADD KEY `tstamp` (`tstamp`);

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `sender` (`sender`,`recipient`),
  ADD KEY `action` (`action`),
  ADD KEY `tstamp` (`tstamp`),
  ADD KEY `smtp_ip` (`smtp_ip`),
  ADD KEY `sender_ip` (`sender_ip`);


ALTER TABLE `archive_stat`
  MODIFY `ID` int(255) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `custom_level`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `domains`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `live_stat`
  MODIFY `ID` int(255) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `mail_limit`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `nodes`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `senders`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `transactions`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;