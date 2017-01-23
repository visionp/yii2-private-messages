DROP TABLE IF EXISTS `auth_assignment`;

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`));


INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`)
VALUES
	('admin','1',1467200946),
	('admin','3',1485175586),
	('operator','2',1467201863);



# Дамп таблицы auth_item
# ------------------------------------------------------------

DROP TABLE IF EXISTS `auth_item`;

CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`));


INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`)
VALUES
	('admin',1,'Администратор',NULL,NULL,1467200945,1467200945),
	('operator',1,'Оператор',NULL,NULL,1467200945,1467200945);



DROP TABLE IF EXISTS `auth_item_child`;

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`));


INSERT INTO `auth_item_child` (`parent`, `child`)
VALUES
	('admin','operator');



# Дамп таблицы auth_rule
# ------------------------------------------------------------

DROP TABLE IF EXISTS `auth_rule`;

CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
);



# Дамп таблицы messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) DEFAULT NULL,
  `whom_id` int(11) NOT NULL,
  `message` varchar(750) NOT NULL,
  `status` int(11) DEFAULT '0',
  `is_delete_from` int(11) DEFAULT '0',
  `is_delete_whom` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx-messages-from_id` (`from_id`),
  KEY `idx-messages-whom_id` (`whom_id`),
  CONSTRAINT `fk-messages-from_id-user-id` FOREIGN KEY (`from_id`) REFERENCES `user` (`id`),
  CONSTRAINT `fk-messages-whom_id-user-id` FOREIGN KEY (`whom_id`) REFERENCES `user` (`id`)
);

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;

INSERT INTO `messages` (`id`, `from_id`, `whom_id`, `message`, `status`, `is_delete_from`, `is_delete_whom`, `created_at`, `updated_at`)
VALUES
	(1,1,2,'werer',2,1,0,1484984787,1484984787),
	(2,1,2,'erer',2,1,0,1484984798,1484984798),
	(3,1,2,'test2\n',2,1,0,1484984831,1484984895),
	(4,2,1,'answer',2,0,1,1484984867,1484984867),
	(5,2,1,'qw\n',2,0,1,1484984906,1484984906),
	(6,1,2,'rtret\n',2,1,0,1484987678,1484987678),
	(7,1,2,'werwer',2,1,0,1484987693,1484987693),
	(8,1,2,'thfhrty',2,1,0,1485106705,1485106705),
	(9,1,2,'test1986',2,1,0,1485107871,1485107871),
	(10,2,1,'testanswer\n',2,0,1,1485107890,1485107890),
	(11,1,2,'arsd',2,1,0,1485107980,1485107980),
	(12,1,2,'kjghjvhg',2,1,0,1485108508,1485108508),
	(13,1,2,'khjhi',2,1,0,1485108512,1485108512),
	(14,1,2,'hjhk',2,1,0,1485108515,1485108515),
	(15,1,2,'last',2,1,0,1485108535,1485108535),
	(16,1,2,'warwer',2,1,0,1485108718,1485108718),
	(17,1,2,'LastLLL',2,1,0,1485108757,1485108757),
	(18,1,2,'AS',2,1,0,1485108913,1485108913),
	(19,2,1,'ewrewt',2,0,1,1485108953,1485108953),
	(20,2,1,'!!!!\n',2,0,1,1485109028,1485109028),
	(21,1,2,'&amp;&amp;&amp;\n',2,1,0,1485109043,1485109043),
	(22,1,2,'www',2,1,0,1485109251,1485109251),
	(23,1,2,'er',2,1,0,1485109574,1485109574),
	(24,1,2,'df',2,1,0,1485109599,1485109599),
	(25,1,2,'lasq',2,1,0,1485109671,1485109671),
	(26,1,2,'rddf',2,1,0,1485109735,1485111914),
	(27,2,1,'arerer',2,0,1,1485109795,1485109795),
	(28,1,2,'hello\n',2,1,0,1485111233,1485111233),
	(29,1,2,'fr',2,1,0,1485111247,1485111917),
	(30,1,2,'dgfd',2,1,0,1485111906,1485111916),
	(31,1,2,'retret',2,1,0,1485158378,1485158378),
	(32,2,1,'dfsf',2,0,1,1485158601,1485158601),
	(33,2,1,'dsfds',2,0,1,1485158602,1485158602),
	(34,2,1,'qwqewe',2,0,1,1485158918,1485158918),
	(35,2,1,'esfdfgdfg',2,0,1,1485160295,1485160295),
	(36,2,1,'tert',2,0,1,1485160298,1485160298),
	(37,2,1,'end',2,0,1,1485160327,1485160327),
	(38,2,1,'fgfg\n',2,0,1,1485160368,1485160368),
	(39,2,1,'test',2,0,1,1485160864,1485160864),
	(40,1,2,'retert',2,1,0,1485160896,1485160896),
	(41,2,1,'qqq',2,0,1,1485160900,1485160900),
	(42,1,2,'r6rtu7',1,1,0,1485161567,1485168319),
	(43,1,2,'ere',1,1,0,1485168102,1485168309),
	(44,1,2,'erer',1,1,0,1485168308,1485168310),
	(45,1,2,'ee',1,1,0,1485168316,1485168446),
	(46,1,2,'sdsdsd',1,1,0,1485168441,1485168610),
	(47,1,2,'kfgjhfdjkg\n',1,1,0,1485168605,1485168777),
	(48,1,2,'retrte',1,1,0,1485168771,1485168776),
	(49,1,3,'test',1,0,0,1485175612,1485175612),
	(50,1,2,'wwww',1,0,0,1485175620,1485175620);

/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;


# Дамп таблицы user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
);


INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'admin','EsvIKh17D5h8pAjizRcJ93TaOzTMGA_k','$2y$13$G5jIywhO./qWk49w6hDpXOR9Wkkcj/P/JvsutSr78uMqDB5dYvOqS',NULL,'admin@a.a',10,1467200946,1467200946),
	(2,'operator','ZEifmD7_IL8371ikDRvH6NQQB7v4XzVG','$2y$13$RyVq9BJonDohxsLMZGWUoOXCL3JXNXGb5TjspaVyQtSCysrbQSLM2',NULL,'operator@a.a',10,1467201863,1467201863),
	(3,'user','ov7LWGzoX-_eTIR0dPk1FxafEBOURZGw','$2y$13$sZlZNzOIpfwf8D0gEAPPpO2efitCHuqjzkJHirRxzfnCxHbe7Z4le',NULL,'user@a.a',10,1485175586,1485175586);
