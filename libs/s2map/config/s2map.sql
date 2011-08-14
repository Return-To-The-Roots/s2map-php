-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 14. August 2011 um 17:33
-- Server Version: 5.1.49
-- PHP-Version: 5.3.3-1ubuntu9.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `dev_s2map`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `s2map`
--

CREATE TABLE IF NOT EXISTS `s2map` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) COLLATE latin1_german1_ci NOT NULL,
  `name` varchar(20) COLLATE latin1_german1_ci NOT NULL,
  `author` varchar(20) COLLATE latin1_german1_ci NOT NULL,
  `players` int(1) NOT NULL,
  `type` int(1) NOT NULL,
  `width` int(10) NOT NULL,
  `height` int(10) NOT NULL,
  `map` longblob,
  `preview` longblob,
  `last_changed` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=411 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
