CREATE TABLE `admin` (
  `yyuid` bigint(20) NOT NULL,
  `udb` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `nickname` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`yyuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员';

CREATE TABLE `user` (
  `yyuid` bigint(20) NOT NULL,
  `udb` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `nickname` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`yyuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户';

