CREATE TABLE `backend_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,  
  `meeting_id` varchar(255) DEFAULT NULL,
  `is_active` enum('1','0') DEFAULT '0',
  `ipaddress` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE `backend_users_login` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `reg_user_id` bigint(20) DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `logon` datetime DEFAULT NULL,
  `logout` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `backend_users_mails` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `reg_user_id` bigint(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `reason` longtext DEFAULT NULL,
  `regon` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
