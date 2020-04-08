CREATE TABLE IF NOT EXISTS `#__bookpro_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `from` mediumtext NOT NULL,
  `to` mediumtext NOT NULL,
  `recipient_info` text NOT NULL,
  `is_paid` tinyint(1) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `package` text NOT NULL,
  `is_booked` tinyint(1) NOT NULL,
  `delivery_code` varchar(20) NOT NULL,
  `discount` decimal(15,4) NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `sender_validate` tinyint(1) NOT NULL,
  `recipient_validate` tinyint(1) NOT NULL,
  `trip_status` tinyint(1) NOT NULL,
  `is_accepted` tinyint(1) NOT NULL,
  `comment` text NOT NULL,
  `params` text NOT NULL,
  `distance` float(10,2) NOT NULL,
  `tax` float(2,2) NOT NULL,
  `transport_type` int(11) NOT NULL,
  `created_time` datetime NOT NULL,
  `currency` varchar(10) NOT NULL,
  `trip_start_time` datetime,
  PRIMARY KEY (`id`),
CONSTRAINT `fk_orders_customer_id` FOREIGN KEY (`customer_id`)
REFERENCES `#__bookpro_customer` (`id`)
ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
alter table #__bookpro_orders add `note` mediumtext;
ALTER table #__bookpro_orders add `trip_location` text;