CREATE TABLE `mc_info` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;






sqlite:

CREATE TABLE if not exists mc_info (
  mid varchar(128) primary key,
  cover_thumb varchar(255),
  cover_medium varchar(255),
  cover_large varchar(255),
  cover_hd varchar(255),
  title char(64) ,
  play_url varchar(255),
  save_path varchar(255) ,
  duration int(11) ,
  status tinyint(4) ,
  created bigint
);

