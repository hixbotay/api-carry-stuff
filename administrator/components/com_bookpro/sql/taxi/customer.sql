--
-- Database: `taxi`
--

-- --------------------------------------------------------

--
-- Table structure for table `m709q_bookpro_customer`
--

CREATE TABLE IF NOT EXISTS `#__bookpro_customer` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `address` varchar(50) NOT NULL,
  `city` varchar(30) NOT NULL,
  `postalcode` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `password` varchar(30) NOT NULL,
  `company_name` varchar(50) NOT NULL COMMENT 'for enterprise and driver',
  `function` varchar(50) NOT NULL COMMENT 'for enterprise and driver',
  `user_type` tinyint(1) NOT NULL COMMENT 'sperate type registration',
  `active_code` varchar(50) NOT NULL COMMENT 'only for Particular',
  `registration_date` date NOT NULL,
  `active` tinyint(1) NOT NULL COMMENT 'status of customer after validation',
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='registration and user' AUTO_INCREMENT=1 ;

ALTER table #__bookpro_customer change `active_code` `params` text;
