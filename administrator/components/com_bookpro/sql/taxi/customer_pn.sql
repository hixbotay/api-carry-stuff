create table #__bookpro_customer_pn (
`id` int(11) NOT NULL AUTO_INCREMENT,
`user_id` int(11),
 `device_token` varchar(200),
`os` varchar(50),
`api_key` varchar(50),
 `push_alert` tinyint(1) default 0,
 `push_sound` tinyint(1) default 0,
  `push_badge` tinyint(1) default 0,
key `user_id` (`user_id`),
PRIMARY KEY (`id`),
CONSTRAINT `#__bookpro_customer_pn_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `#__bookpro_customer` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


