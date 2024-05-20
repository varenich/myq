CREATE TABLE `EVENT` (
  `event_name` varchar(200) NOT NULL,
  `subscriber_name` varchar(200) NOT NULL,
  `data` text,
  `registration_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `publisher_name` varchar(200) NOT NULL,
  PRIMARY KEY (`event_id`),
  KEY `subscriber` (`subscriber_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `SUBSCRIPTION` (
  `subscriber_name` varchar(200) NOT NULL,
  `event_name` varchar(200) NOT NULL,
  PRIMARY KEY (`subscriber_name`,`event_name`),
  KEY `subscriber` (`subscriber_name`),
  KEY `event` (`event_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
