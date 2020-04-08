
-- --------------------------------------------------------

--
-- Table structure for table `#__bookpro_coupon`
--

CREATE TABLE IF NOT EXISTS `#__bookpro_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `params` mediumtext NOT NULL,
  `date` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


