#!/usr/local/php/bin/php
<?php

include 'config.php';
include 'SQLiteModel.php';

$m = new Model('', 'mysql_prod');

$m->query("CREATE TABLE `mc_info` (
  `mid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_thumb` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_medium` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_large` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_hd` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `play_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `save_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$m->query("CREATE TABLE `mc_relate` (
  `relate_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `mid_type` (`mid`,`type_id`) USING BTREE,
  KEY `type_id` (`type_id`),
  KEY `mid` (`mid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='音频信息与归类关联表'");

$m->query("CREATE TABLE `mc_type` (
  `type_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '归类，也就是接口的mc_id',
  `type_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");