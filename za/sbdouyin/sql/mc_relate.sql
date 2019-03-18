CREATE TABLE `mc_relate` (
  `relate_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mid` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`relate_id`),
  UNIQUE KEY `mid_type` (`mid`,`type_id`) USING BTREE,
  KEY `type_id` (`type_id`),
  KEY `mid` (`mid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='音频信息与归类关联表';



sqlite:

CREATE TABLE if not exists mc_relate (
  relate_id bigint primary key,
  mid varchar(128),
  type_id varchar(128),
);

