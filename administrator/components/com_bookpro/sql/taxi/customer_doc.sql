
CREATE TABLE IF NOT EXISTS `#__bookpro_customer_doc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `customer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `#_bookpro_customer_doc_1` FOREIGN KEY (`customer_id`)
REFERENCES `#__bookpro_customer` (`id`)
ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
