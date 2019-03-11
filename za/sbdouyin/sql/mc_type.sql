CREATE TABLE `mc_type` (
  `type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_cover` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `idx_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

