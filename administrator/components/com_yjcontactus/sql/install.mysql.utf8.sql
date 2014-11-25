-- $Id: install.mysql.utf8.sql 74 2010-12-01 22:04:52Z chdemko $

CREATE TABLE IF NOT EXISTS #__yj_contactus_departments (
`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ordering` INT( 11 ) NOT NULL ,
`published` TINYINT( 3 ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`description` TEXT NOT NULL ,
`message` TEXT NOT NULL,
`checked_out` int(11) NOT NULL,
`checked_out_time` datetime NOT NULL,
`enabled` enum('0','1') NOT NULL,
`upload` enum('0','1') NOT NULL,
`email` VARCHAR( 255 ) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS #__yj_contactus_forms (
`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ordering` INT( 11 ) NOT NULL ,
`published` TINYINT( 3 ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`email` VARCHAR( 255 ) NOT NULL ,
`departments` varchar(255) NOT NULL,
`checked_out` INT( 11 ) NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`captcha` ENUM( '0', '1' ) NOT NULL,
`item_id` INT( 11 ) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS #__yj_contactus_settings (
`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`upload_folder` VARCHAR( 255 ) NOT NULL DEFAULT 'yjcontactus' 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS #__yj_contactus_captcha (
`id` INT( 255 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`session_id_contactus` VARCHAR( 255 ) NOT NULL ,
`captcha` INT( 255 ) NOT NULL,
`attachement` varchar(255) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
