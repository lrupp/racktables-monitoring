DROP table if exists MonitoringServer;
CREATE TABLE `MonitoringServer` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`name` char(255),
	`base_url` char(255),
	`css` char(255),
	`javascript` char(255),
	`regularexp` char(255),
	`username` char(255),
	`password` char(255),
        PRIMARY KEY (`id`)
) ENGINE=InnoDB;
