SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `books` (
  `code` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `short` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `name` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `is_published` tinyint(1) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `json` (
  `id` varchar(255) collate utf8_bin NOT NULL,
  `v` tinyint(4) NOT NULL,
  `json` mediumtext collate utf8_bin NOT NULL,
  `username` varchar(255) collate utf8_bin NOT NULL,
  `updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`,`v`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `notes` (
  `code` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `seccode` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `ref_title` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `ref_url` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `username` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL default '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `comment` mediumtext character set utf8 collate utf8_bin NOT NULL,
  `tags` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `content` mediumtext character set utf8 collate utf8_bin NOT NULL,
  `pos` float NOT NULL,
  PRIMARY KEY  (`code`),
  KEY `seccode` (`seccode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `cutnote`.`updatesection`;
DELIMITER //
CREATE TRIGGER `cutnote`.`updatesection` AFTER UPDATE ON `cutnote`.`notes`
 FOR EACH ROW BEGIN
        UPDATE sections SET updated_at = NEW.updated_at WHERE code = NEW.seccode;
    END
//
DELIMITER ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sections` (
  `code` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `bookcode` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `name` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `pos` float NOT NULL,
  `updated_at` timestamp NOT NULL default '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`code`),
  KEY `sections_ibfk_1` (`bookcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TRIGGER IF EXISTS `cutnote`.`updatebooks`;
DELIMITER //
CREATE TRIGGER `cutnote`.`updatebooks` AFTER UPDATE ON `cutnote`.`sections`
 FOR EACH ROW BEGIN
        UPDATE books SET updated_at = NEW.updated_at WHERE code = NEW.bookcode;
    END
//
DELIMITER ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `login` varchar(40) NOT NULL default '',
  `password` varchar(255) default NULL,
  `created_at` datetime default NULL,
  `last_login` datetime default NULL,
  `email` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `enabled` tinyint(1) default '0',
  `last_active` datetime default NULL,
  `bytes_used` int(11) default '0',
  `disk_used` int(11) default '0',
  PRIMARY KEY  (`login`),
  KEY `updated` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`login`, `password`, `created_at`, `last_login`, `email`, `name`, `enabled`, `last_active`, `bytes_used`, `disk_used`) VALUES
('demo', '$2wdSAngDShks', NULL, NULL, NULL, NULL, 0, NULL, 0, 0);

ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`seccode`) REFERENCES `sections` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`bookcode`) REFERENCES `books` (`code`) ON DELETE CASCADE ON UPDATE CASCADE;
