-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Gép: localhost
-- Létrehozás ideje: 2017. Sze 12. 04:00
-- Kiszolgáló verziója: 10.2.7-MariaDB-log
-- PHP verzió: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `mvcp_aspf`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `archive_stat`
--

CREATE TABLE `archive_stat` (
  `ID` int(255) UNSIGNED NOT NULL,
  `local_mail` varchar(255) NOT NULL,
  `in` int(10) UNSIGNED NOT NULL,
  `out` int(10) UNSIGNED NOT NULL,
  `tstamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `custom_level`
--

CREATE TABLE `custom_level` (
  `ID` int(10) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `level` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `domains`
--

CREATE TABLE `domains` (
  `ID` int(10) UNSIGNED NOT NULL,
  `domain` varchar(255) NOT NULL,
  `type` enum('whitelist','blacklist','pending','') NOT NULL,
  `expire` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `live_stat`
--

CREATE TABLE `live_stat` (
  `ID` int(255) UNSIGNED NOT NULL,
  `local_mail` varchar(255) NOT NULL,
  `in` int(10) UNSIGNED NOT NULL,
  `out` int(10) UNSIGNED NOT NULL,
  `first` int(10) UNSIGNED NOT NULL,
  `last` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `mail_limit`
--

CREATE TABLE `mail_limit` (
  `ID` int(10) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `limit` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `nodes`
--

CREATE TABLE `nodes` (
  `ID` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip4` varchar(255) NOT NULL,
  `ip6` varchar(255) NOT NULL,
  `settings` text NOT NULL,
  `last_seen` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `senders`
--

CREATE TABLE `senders` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `smtp_ip` varchar(255) NOT NULL,
  `smtp_name` varchar(255) NOT NULL,
  `sender_ip` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `type` enum('whitelist','blacklist','cache') NOT NULL,
  `expire` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `state`
--

CREATE TABLE `state` (
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `data` blob NOT NULL,
  `tstamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `transactions`
--

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

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `archive_stat`
--
ALTER TABLE `archive_stat`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `local_mail` (`local_mail`),
  ADD KEY `tstamp` (`tstamp`);

--
-- A tábla indexei `custom_level`
--
ALTER TABLE `custom_level`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `address` (`address`),
  ADD KEY `domain` (`domain`);

--
-- A tábla indexei `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD KEY `expire` (`expire`);

--
-- A tábla indexei `live_stat`
--
ALTER TABLE `live_stat`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `local_mail` (`local_mail`),
  ADD KEY `tstamp` (`last`),
  ADD KEY `first` (`first`);

--
-- A tábla indexei `mail_limit`
--
ALTER TABLE `mail_limit`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `address` (`address`),
  ADD KEY `domain` (`domain`);

--
-- A tábla indexei `nodes`
--
ALTER TABLE `nodes`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- A tábla indexei `senders`
--
ALTER TABLE `senders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `sender` (`address`),
  ADD KEY `type` (`type`),
  ADD KEY `expire` (`expire`),
  ADD KEY `smtp_ip` (`smtp_ip`);

--
-- A tábla indexei `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`key`),
  ADD KEY `tstamp` (`tstamp`);

--
-- A tábla indexei `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `sender` (`sender`,`recipient`),
  ADD KEY `action` (`action`),
  ADD KEY `tstamp` (`tstamp`),
  ADD KEY `smtp_ip` (`smtp_ip`),
  ADD KEY `sender_ip` (`sender_ip`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `archive_stat`
--
ALTER TABLE `archive_stat`
  MODIFY `ID` int(255) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT a táblához `custom_level`
--
ALTER TABLE `custom_level`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT a táblához `domains`
--
ALTER TABLE `domains`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT a táblához `live_stat`
--
ALTER TABLE `live_stat`
  MODIFY `ID` int(255) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT a táblához `mail_limit`
--
ALTER TABLE `mail_limit`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT a táblához `nodes`
--
ALTER TABLE `nodes`
  MODIFY `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT a táblához `senders`
--
ALTER TABLE `senders`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2112;
--
-- AUTO_INCREMENT a táblához `transactions`
--
ALTER TABLE `transactions`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13219;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
