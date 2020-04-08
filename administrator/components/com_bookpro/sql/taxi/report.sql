
create table IF NOT EXISTS `#__bookpro_report` (
	`id` int(11) not null AUTO_INCREMENT,
	`customer_id` int(11), 
	`desc` mediumtext, 
	`created` datetime, 
	primary key (`id`), 
	key `customer_id` (`customer_id`), 
	constraint `#__bookpro_report_customer_1` FOREIGN KEY (`customer_id`) references `#__bookpro_customer` (`id`) ON delete cascade 
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
