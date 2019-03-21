/*
Navicat MySQL Data Transfer

Source Server         : 61.160.36.225
Source Server Version : 50554
Source Host           : 61.160.36.225:3306
Source Database       : douyin_media

Target Server Type    : MYSQL
Target Server Version : 50554
File Encoding         : 65001

Date: 2019-03-19 19:39:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `mc_type`
-- ----------------------------
DROP TABLE IF EXISTS `mc_type`;
CREATE TABLE `mc_type` (
  `type_id` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '归类，也就是接口的mc_id',
  `type_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type_cover` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of mc_type
-- ----------------------------
INSERT INTO `mc_type` VALUES ('1', '流行', 'typecover/1.jpg');
INSERT INTO `mc_type` VALUES ('11', '翻唱大挑战', 'typecover/11.png');
INSERT INTO `mc_type` VALUES ('114', '生活', 'typecover/114.jpg');
INSERT INTO `mc_type` VALUES ('12', '经典', 'typecover/12.jpg');
INSERT INTO `mc_type` VALUES ('18', '说唱', 'typecover/18.jpg');
INSERT INTO `mc_type` VALUES ('20', '国风', 'typecover/20.jpg');
INSERT INTO `mc_type` VALUES ('31', 'SWAG', 'typecover/31.jpg');
INSERT INTO `mc_type` VALUES ('4', 'ACG', 'typecover/4.jpg');
INSERT INTO `mc_type` VALUES ('5', '日韩', 'typecover/5.jpg');
INSERT INTO `mc_type` VALUES ('6601750711774644995', '爱吃爱旅行', 'typecover/6601750711774644995.jpg');
INSERT INTO `mc_type` VALUES ('6616178533779802884', '热歌榜', 'typecover/6616178533779802884.jpg');
INSERT INTO `mc_type` VALUES ('6616178774952119044', '飙升榜', 'typecover/6616178774952119044.jpg');
INSERT INTO `mc_type` VALUES ('6631448622997179150', '新歌', 'typecover/6631448622997179150.jpg');
INSERT INTO `mc_type` VALUES ('84', '配乐', 'typecover/84.jpg');
INSERT INTO `mc_type` VALUES ('860', '原创音乐', 'typecover/860.jpg');
INSERT INTO `mc_type` VALUES ('865', '热歌榜', 'typecover/865.png');
INSERT INTO `mc_type` VALUES ('866', '飙升榜', 'typecover/866.png');
INSERT INTO `mc_type` VALUES ('87', '激萌', 'typecover/87.jpg');
INSERT INTO `mc_type` VALUES ('9', '影视原声', 'typecover/9.jpg');
INSERT INTO `mc_type` VALUES ('90', '搞怪', 'typecover/90.jpg');
