
-- --------------------------------------------------------

--
-- Table structure for table `tx_cache`
--

CREATE TABLE IF NOT EXISTS `tx_cache` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `tx_id` varchar(64) NOT NULL,
  `block_height` int(9) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tx_id` (`tx_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tx_payments`
--

CREATE TABLE IF NOT EXISTS `tx_payments` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `block_height` int(9) DEFAULT NULL,
  `tx_id` varchar(64) DEFAULT NULL,
  `address` varchar(40) DEFAULT NULL,
  `value` decimal(20,8) DEFAULT NULL,
  `vout` int(9) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tx_sent`
--

CREATE TABLE IF NOT EXISTS `tx_sent` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `inputs` text NOT NULL,
  `outputs` text NOT NULL,
  `address` varchar(40) NOT NULL,
  `unique_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `watched_addresses`
--

CREATE TABLE IF NOT EXISTS `watched_addresses` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `json_inputs` text NOT NULL,
  `partially_signed` text NOT NULL,
  `partially_signed_time` varchar(20) NOT NULL,
  `complete` enum('0','1') NOT NULL DEFAULT '0',
  `final_id` varchar(64) NOT NULL,
  `address` varchar(40) NOT NULL,
  `redeemScript` text NOT NULL,
  `public_key1` varchar(150) NOT NULL,
  `public_key2` varchar(150) NOT NULL,
  `public_key3` varchar(150) NOT NULL,
  `unique_id` varchar(64) NOT NULL,
  `n` int(9) NOT NULL,
  `site_priv_key` varchar(100) NOT NULL,
  `unsigned_transaction` text NOT NULL,
  `destination` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

