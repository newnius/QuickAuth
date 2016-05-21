account
create table `account`(
  `username` varchar(12) primary key,
  `password` char(64) not null,
  `email` varchar(45) not null unique ,
  index(`email`),
  `auth_key` char(32) not null,
  `salt` char(64) not null,
  `verified` char not null default 'f',
  `last_ip` bigint,
  `last_time` int,
  `reg_time` int,
  `reg_ip` bigint
  )ENGINE = MyISAM DEFAULT CHARSET = utf8;
  

signin_log
create table `signin_log`(
  `log_id` int AUTO_INCREMENT,
  primary key(`log_id`),
  `account` varchar(45) NOT NULL,
  index(`account`),
  `accepted` char NOT NULL,
  `time` int NOT NULL,
  `ip` bigint NOT NULL
)ENGINE = MyISAM DEFAULT CHARSET = utf8;


email_log
create table `email_log`(
  `log_id` int NOT NULL AUTO_INCREMENT,
  primary key(`log_id`),
  `email` varchar(45) NOT NULL,
  index(`email`),
  `time` int,
  index(`time`),
  `ip` bigint,
  index(`ip`)
)ENGINE = MyISAM DEFAULT CHARSET = utf8;











