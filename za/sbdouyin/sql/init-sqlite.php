#!/usr/local/php/bin/php
<?php

include 'config.php';
include 'SQLiteModel.php';

$m = new SQLiteModel("{$GLOBALS['sqlite']['dbpath']}/mc.sqlite");

$m->query("CREATE TABLE if not exists mc_info (
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
)");

$m->query("CREATE TABLE if not exists mc_relate (
  relate_id bigint primary key,
  mid varchar(128),
  type_id varchar(128)
)");

$m->query("CREATE TABLE if not exists mc_type (
  type_id varchar(128) primary key,
  type_name varchar(64),
  type_cover varchar(255)
)");