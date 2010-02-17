--
-- Table structure for table `ext_contact_person`
--

CREATE TABLE `ext_contact_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `date_update` int(10) unsigned NOT NULL,
  `id_person_create` smallint(5) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  `username` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `shortname` varchar(11) NOT NULL,
  `gender` varchar(2) NOT NULL,
  `title` varchar(64) NOT NULL,
  `birthday` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_company`
--

CREATE TABLE `ext_contact_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `date_update` int(10) unsigned NOT NULL,
  `id_person_create` smallint(5) unsigned NOT NULL,
  `deleted` tinyint(2) NOT NULL DEFAULT '0',
  `title` tinytext NOT NULL,
  `shortname` tinytext NOT NULL,
  `id_currency` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `date_enter` int(10) unsigned NOT NULL DEFAULT '0',
  `is_internal` tinyint(1) NOT NULL DEFAULT '0',
  `ext_projectbilling_reduction` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_address`
--

CREATE TABLE `ext_contact_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `date_update` int(10) unsigned NOT NULL,
  `id_person_create` smallint(5) unsigned NOT NULL,
  `deleted` tinyint(2) NOT NULL,
  `id_addresstype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_country` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id_holidayset` int(11) NOT NULL DEFAULT '0',
  `street` varchar(128) NOT NULL,
  `postbox` varchar(32) NOT NULL,
  `city` varchar(48) NOT NULL,
  `region` varchar(32) NOT NULL,
  `zip` mediumtext NOT NULL,
  `comment` varchar(255) NOT NULL,
  `is_preferred` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_contactinfo`
--

CREATE TABLE `ext_contact_contactinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_create` int(10) unsigned NOT NULL DEFAULT '0',
  `date_update` int(10) unsigned NOT NULL,
  `id_person_create` smallint(5) unsigned NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `id_contactinfotype` tinytext NOT NULL,
  `info` tinytext NOT NULL,
  `preferred` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_contactinfotype`
--

CREATE TABLE `ext_contact_contactinfotype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_create` int(11) NOT NULL,
  `date_update` int(11) NOT NULL,
  `id_person_create` smallint(5) unsigned NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `category` smallint(5) unsigned NOT NULL,
  `key` varchar(20) NOT NULL,
  `title` varchar(48) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_jobtype`
--

CREATE TABLE `ext_contact_jobtype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(48) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_mm_company_address`
--

CREATE TABLE `ext_contact_mm_company_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_company` int(10) unsigned NOT NULL DEFAULT '0',
  `id_address` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_mm_company_contactinfo`
--

CREATE TABLE `ext_contact_mm_company_contactinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_company` int(10) unsigned NOT NULL DEFAULT '0',
  `id_contactinfo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_mm_company_person`
--

CREATE TABLE `ext_contact_mm_company_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_company` int(10) unsigned NOT NULL DEFAULT '0',
  `id_person` smallint(5) unsigned NOT NULL,
  `id_workaddress` smallint(6) NOT NULL,
  `id_jobtype` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_mm_person_address`
--

CREATE TABLE `ext_contact_mm_person_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id_address` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_mm_person_contactinfo`
--

CREATE TABLE `ext_contact_mm_person_contactinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id_contactinfo` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `ext_contact_mm_person_role`
--

CREATE TABLE `ext_contact_mm_person_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id_role` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;