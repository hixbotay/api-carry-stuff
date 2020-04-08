CREATE TABLE IF NOT EXISTS `#__bookpro_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,  
  `total` decimal(15,4),
  `params` text,  
  key `order_id` (`order_id`),
  PRIMARY KEY (`id`),
CONSTRAINT `fk_transaction_order` FOREIGN KEY (`order_id`) REFERENCES `#__bookpro_orders` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER table `#__bookpro_transaction` add `success` tinyint(1) default 0;
alter table #__bookpro_transaction add `created` datetime;
alter table #__bookpro_transaction add `tx_id` varchar(100);