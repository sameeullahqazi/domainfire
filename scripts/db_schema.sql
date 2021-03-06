-- PWD: Q<lX<ZVXQ2yv

DROP TABLE IF EXISTS `registrants`;
CREATE TABLE `registrants`
(
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`first_name` varchar(50),
`last_name` varchar(50),
`address1` varchar(50),
`address2` varchar(50),
`city` varchar(50),
`state` varchar(20),
`postal_code` varchar(10),
`country` varchar(30),
`phone` varchar(20),
`email` varchar(70),
`org_name` varchar(50),
`job_title` varchar(50),
`created` timestamp default current_timestamp,
`modified` timestamp null,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions`
(
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`registrant_id` int(10) NOT NULL,
`domain_name` varchar(70),
`years` int,
`registered` tinyint(1),
`amount` decimal(14, 2),
`response_code` int,
`response_text` text,
`response_error` text,
`domain_id` bigint(33),
`order_id`	bigint(33),
`transaction_id` bigint(33),
`stripe_charge_id` varchar(50),
`state` varchar(50),
`created` timestamp default current_timestamp,
`modified` timestamp null,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `domains_data`;
CREATE TABLE `domains_data`
(
`id` bigint(33) unsigned NOT NULL AUTO_INCREMENT,
`domain_name` varchar(70),
`time_created` timestamp null,
`date_month_year` varchar(40),
`price_symbol` varchar(1),
`price_value` decimal(14, 2),
`price_currency` varchar(5),
`broker` varchar(40),
`file_name` varchar(70),
`created` timestamp default current_timestamp,
PRIMARY KEY (`id`),
UNIQUE KEY `domain_name_unique` (`domain_name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
