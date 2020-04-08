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