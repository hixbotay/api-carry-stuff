
-- --------------------------------------------------------

--
-- Table structure for table `#__bookpro_coupon`
--

CREATE TABLE IF NOT EXISTS `#__bookpro_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `value` decimal(15,2) NOT NULL,
  `subtract_type` tinyint(1),
  `title` varchar(50) NOT NULL,
  `total` int(10) NOT NULL COMMENT 'Total of the code',
  `remain` int(10) NOT NULL COMMENT 'Total code remain',
  `state` tinyint(1) NOT NULL,
  `publish_date` date NOT NULL,
  `unpublish_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


