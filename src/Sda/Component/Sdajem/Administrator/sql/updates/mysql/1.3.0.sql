CREATE TABLE IF NOT EXISTS `#__sdajem_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `users_user_id` INT UNSIGNED NOT NULL COMMENT 'Foreign Key to #__users',
  `sdajem_event_id` INT UNSIGNED NOT NULL COMMENT 'Foreign Key to #__yajem_events',
  `comment` MEDIUMTEXT NOT NULL ,
  `timestamp` DATETIME,
  `commentReadBy` varchar(255) DEFAULT NULL NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_comment_time` (`timestamp` DESC))
  ENGINE=InnoDB
  DEFAULT CHARSET=`utf8mb4` DEFAULT COLLATE=`utf8mb4_unicode_ci`;