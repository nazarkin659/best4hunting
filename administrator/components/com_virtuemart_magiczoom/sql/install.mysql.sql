CREATE TABLE IF NOT EXISTS `#__virtuemart_magiczoom_config` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `profile` VARCHAR(128) NOT NULL DEFAULT '',
    `name` VARCHAR(128) NOT NULL DEFAULT '',
    `value` TEXT,
    `default` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

