create table #__bookpro_customer_pn_log (
`id` int(11) NOT NULL AUTO_INCREMENT,
`pn_id` varchar(200),
`message` varchar(255) NOT NULL,
`badge` int(11) NOT NULL,
`created_time` datetime NOT NULL,
`status` tinyint(1),
PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


