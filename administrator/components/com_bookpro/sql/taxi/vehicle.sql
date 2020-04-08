CREATE TABLE IF NOT EXISTS `#__bookpro_vehicle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `vehicle_type_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `desc` varchar(200) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `default` tinyint(1) NOT NULL,
  `current` tinyint(1) NOT NULL,
  `capacity` varchar(50) NOT NULL,
  `price` float(10,5) NOT NULL,
  `params` mediumtext,
  KEY `driver_id` (`driver_id`),
  KEY `vehicle_type_id` (`vehicle_type_id`),
CONSTRAINT `#__vehicle_driver_id` FOREIGN KEY (`driver_id`)
REFERENCES `#__bookpro_customer` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
CONSTRAINT `#__vehicle_type_id` FOREIGN KEY (`vehicle_type_id`)
REFERENCES `#__bookpro_vehicle_type` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;