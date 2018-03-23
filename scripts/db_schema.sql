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

