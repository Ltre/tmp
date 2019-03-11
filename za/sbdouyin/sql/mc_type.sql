CREATE TABLE `mc_type` (
  `type_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '归类，也就是接口的mc_id',
  `type_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

