CREATE TABLE IF NOT EXISTS `#__sdajem_interest` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`access` int(10) unsigned NOT NULL DEFAULT 0,
	`alias` varchar(400),
	`created` datetime,
	`created_by` int(10) unsigned,
	`created_by_alias` varchar(255),
	`state` tinyint(3) NOT NULL DEFAULT 0,
	`ordering` int(11) NOT NULL DEFAULT 0,
	`event_id` INT UNSIGNED NOT NULL COMMENT 'Foreign Key to #__yajame_events',
  	`users_user_id` INT UNSIGNED NULL COMMENT 'Foreign Key to #__users',
  	`status` TINYINT(1) NOT NULL DEFAULT 0,
  	`comment` MEDIUMTEXT  NULL,
	PRIMARY KEY (`id`),
	KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB
  DEFAULT CHARSET=`utf8mb4` DEFAULT COLLATE=`utf8mb4_unicode_ci`;

INSERT INTO `#__sdajem_interest` (id, access, alias, created, created_by, created_by_alias, state, ordering, event_id, users_user_id, status, comment)
SELECT id,
       access,
       'interest',
       created,
       created_by,
       created_by_alias,
       state,
       ordering,
       event_id,
       users_user_id,
       status,
       ''
FROM `#__sdajem_attendings`
WHERE event_id IN (
    SELECT id
    FROM `#__sdajem_events`
    WHERE eventStatus = 4
    )
;

DELETE
FROM `#__sdajem_attendings`
WHERE event_id IN (
    SELECT id
    FROM `#__sdajem_events`
    WHERE eventStatus = 4
    )
;