CREATE TABLE IF NOT EXISTS `#__bookpro_session` (
  `session_id` varchar(200) NOT NULL DEFAULT '',
  `time` varchar(14) DEFAULT '',
  `data` mediumtext,
  `userid` int(11) DEFAULT '0',
  `lat` FLOAT( 10, 6 ) NOT NULL ,
  `lng` FLOAT( 10, 6 ) NOT NULL,
  `free` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`session_id`),
  KEY `userid` (`userid`),
  KEY `time` (`time`),
  KEY `lat` (`lat`),
  KEY `lng` (`lng`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bookpro_session` change `session_id` `session_id` varchar(20) NOT NULL;
