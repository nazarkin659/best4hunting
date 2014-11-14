CREATE TABLE IF NOT EXISTS `#__vmreporter_byproduct` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`report_query` TEXT NOT NULL ,
`report_details` LONGTEXT NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__vmreporter_bycategory` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`report_query` TEXT NOT NULL ,
`report_details` LONGTEXT NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__vmreporter_bycustomer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`report_query` TEXT NOT NULL ,
`report_details` LONGTEXT NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__vmreporter_bymanufacturer` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`report_query` TEXT NOT NULL ,
`report_details` LONGTEXT NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__vmreporter_bycountry` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`report_query` TEXT NOT NULL ,
`report_details` LONGTEXT NOT NULL ,
`created_on` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

