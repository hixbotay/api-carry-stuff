CREATE TABLE IF NOT EXISTS `#__jbackend_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Existing user id',
  `daily_requests` int(11) NOT NULL DEFAULT '0' COMMENT 'Max number of daily requests (0=Unlimited)',
  `expiration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` varchar(255) DEFAULT NULL,
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `last_visit` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `current_day` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Current day for daily requests limit',
  `current_hits` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Hits of current day for daily requests limit',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxKey` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jbackend_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `endpoint` int(11) NOT NULL COMMENT 'The menu id associated to the endpoint',
  `access_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=free, 1=user, 2=key',
  `request_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `request_date` date NOT NULL DEFAULT '0000-00-00',
  `error` tinyint(1) NOT NULL DEFAULT '0',
  `error_code` varchar(10) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Existing user id',
  `key` varchar(255) NOT NULL,
  `action` varchar(10) NOT NULL,
  `module` varchar(30) NOT NULL,
  `resource` varchar(50) NOT NULL,
  `duration` float NOT NULL DEFAULT '0' COMMENT 'Execution time in seconds (with microseconds)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;