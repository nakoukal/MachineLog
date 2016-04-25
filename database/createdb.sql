-- Adminer 4.0.2 MySQL dump

SET NAMES utf8;

-- Vytvoreni database
DROP DATABASE IF EXISTS `datalogs` ;
CREATE DATABASE `datalogs` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci */;
USE `datalogs`;

-- vytvoreni tabulek
CREATE TABLE `data_log` (
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'casové razitko ',
  `MeasureTime` datetime DEFAULT NULL COMMENT 'Datum a cas celkoveho mereni',
  `InnerCode` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Jedinecne cislo logu mereni',
  `GlobalResult` enum('OK','NOK') COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Celkový vysledek',
  `BulbCurentResult` enum('OK','NOK') COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Proud zarovka vysledek',
  `BulbCurentMeasured` double  DEFAULT NULL COMMENT 'Proud zarovka namereno (A)',
  `BulbCurentMeasuredMax` double  DEFAULT NULL COMMENT 'Proud zarovka namereno MAX (A)',
  `BulbCurentMeasuredMin` double  DEFAULT NULL COMMENT 'Proud zarovka namereno MIN (A)',
  `BulbVoltageResult` enum('OK','NOK') COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Napeti žárovka výsledek',
  `BulbVoltageMeasured` double  DEFAULT NULL COMMENT 'Napeti zarovka vysledek namereno (V)',
  `TestLengthMeasured` int(10)  DEFAULT NULL COMMENT 'Delka testu (ms)',
  `TestLengthMeasuredMin` int(10)  DEFAULT NULL COMMENT 'Delka testu MIN (ms)',
  `TestLengthMeasuredMax` int(10)  DEFAULT NULL COMMENT 'Delka testu MAX (ms)',
  `TestBlinkShine01` double  DEFAULT NULL COMMENT 'Prubeh testu blikani svit',
  `TestBlinkDark01` double  DEFAULT NULL COMMENT 'Prubeh testu blikani tma',
  `TestBlinkShine02` double  DEFAULT NULL COMMENT 'Prubeh testu blikani svit',
  `TestBlinkDark02` double  DEFAULT NULL COMMENT 'Prubeh testu blikani tma',
  `TestBlinkShine03` double  DEFAULT NULL COMMENT 'Prubeh testu blikani svit',
  `LeaksResults` enum('OK','NOK') COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Tesnost vysledek',
  `LeaksMeasured` double  DEFAULT NULL COMMENT 'Namereny unik (mBar)',
  `LeaksAllowed` double  DEFAULT NULL COMMENT 'Povoleny unik (mBar)',
  `PressureDest` double  DEFAULT NULL COMMENT 'Cilové tlakovani (mBar)',
  `PressureMin` double  DEFAULT NULL COMMENT 'Cilové tlakovani Min (mBar)',
  `PressureMax` double  DEFAULT NULL COMMENT 'Cilové tlakovani Max (mBar)',
  `PressureActual` double  DEFAULT NULL COMMENT 'Natlakovano na (mBar)',
  `PressureTime` int(10)  DEFAULT NULL COMMENT 'Cas tlakovani (ms)',
  `PressureMeasuredTime` int(10)  DEFAULT NULL COMMENT 'Cas mereni tlakovani (ms)',
  `PressureAfterDelaysMin` int(10)  DEFAULT NULL COMMENT 'Tlakovani po prodleve MIN  (ms)',
  `PressureDelays` int(10)  DEFAULT NULL COMMENT 'Tlakovani prodleva  (ms)',
  
  PRIMARY KEY (`Timestamp`),
  KEY `MeasureTime` (`MeasureTime`),
  KEY `GlobalResult` (`GlobalResult`),
  KEY `InnerCode` (`InnerCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `error_log` (
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `LogString` longtext CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `ErrorType` varchar(50) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`Timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- vytvoreni uzivatele pro vkladani dat do database
GRANT USAGE ON *.* TO 'test'@'localhost';
DROP USER 'test'@'localhost';
CREATE USER 'test'@'localhost' IDENTIFIED BY PASSWORD '*94BDCEBE19083CE2A1F959FD02F964C7AF4CFC29';
GRANT ALL PRIVILEGES ON datalogs.* TO 'test'@'localhost';

-- vytvoreni uzivatele pro nahled na data
GRANT USAGE ON *.* TO 'read'@'localhost';
DROP USER 'read'@'localhost';
CREATE USER 'read'@'localhost' IDENTIFIED BY PASSWORD '*2158DEFBE7B6FC24585930DF63794A2A44F22736';
GRANT SELECT ON datalogs.* TO 'read'@'localhost';

-- 2014-03-01 19:11:41