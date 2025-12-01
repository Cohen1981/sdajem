/*
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

CREATE TABLE IF NOT EXISTS `#__sdajem_events`
(
    `id`                 int(10) unsigned    NOT NULL auto_increment,
    `access`             int(10) unsigned    NOT NULL DEFAULT 0,
    `alias`              varchar(400),
    `created`            datetime,
    `created_by`         int(10) unsigned,
    `published`          tinyint(1)          NOT NULL DEFAULT 0,
    `publish_up`         datetime,
    `publish_down`       datetime,
    `state`              tinyint(3)          NOT NULL DEFAULT 0,
    `ordering`           int(11)             NOT NULL DEFAULT 0,
    `title`              VARCHAR(255)        NOT NULL,
    `description`        MEDIUMTEXT          NULL,
    `url`                VARCHAR(255)        NULL,
    `image`              VARCHAR(1024)       NULL,
    `sdajem_location_id` INT UNSIGNED        NULL,
    `hostId`             INT                 NULL COMMENT 'Optional foreign_key to #__contact_details. Use Case: Visiting third party event. The Contact Information of the event hoster goes here. ',
    `organizerId`        INT                 NULL COMMENT 'Optional foreign_key #__contact_details. Use Case: Visiting third party event. Here goes the organizer/ applicant',
    `startDateTime`      DATETIME            NULL COMMENT 'Starting date of the event',
    `endDateTime`        DATETIME            NULL COMMENT 'End date of the event',
    `allDayEvent`        TINYINT(1) UNSIGNED NULL,
    `eventStatus`        TINYINT(1)          NULL     DEFAULT 0 COMMENT 'Optional status of event. 0=open,1=confirmed,2=cancelled',
    `eventCancelled`     TINYINT(1)          NULL     DEFAULT 0 COMMENT 'for marking events as cancelled by host',
    `params`             text,
    `svg`                BLOB                NULL     DEFAULT NULL,
    `registerUntil`      DATETIME            NULL,
    PRIMARY KEY (`id`),
    KEY `idx_events_state` (`published`),
    KEY `idx_events_organizer` (`organizerId`),
    KEY `idx_events_location` (`sdajem_location_id`),
    KEY `idx_events_created_by` (`created_by`),
    KEY `idx_events_startDateTime` (`startDateTime`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__sdajem_locations`
(
    `id`           int(10) unsigned NOT NULL auto_increment,
    `access`       int(10) unsigned NOT NULL DEFAULT 0,
    `alias`        varchar(400),
    `created`      datetime,
    `created_by`   int(10) unsigned,
    `published`    tinyint(1)       NOT NULL DEFAULT 0,
    `publish_up`   datetime,
    `publish_down` datetime,
    `state`        tinyint(3)       NOT NULL DEFAULT 0,
    `ordering`     int(11)          NOT NULL DEFAULT 0,
    `title`        VARCHAR(255)     NOT NULL COMMENT 'the location title',
    `description`  MEDIUMTEXT       NULL COMMENT 'Optional description of the location',
    `url`          VARCHAR(200)     NULL COMMENT 'Optional url/ link to location website',
    `street`       VARCHAR(50)      NULL COMMENT 'street and number',
    `postalCode`   VARCHAR(20)      NULL,
    `city`         VARCHAR(50)      NULL,
    `stateAddress` VARCHAR(50)      NULL,
    `country`      VARCHAR(50)      NULL,
    `latlng`       VARCHAR(50)      NULL COMMENT 'Used to save lat and lng for Google Maps',
    `contactId`    INT              NULL COMMENT 'Optional foreign key to #__contact_details.',
    `image`        VARCHAR(1024)    NULL COMMENT 'Optional teaser image',
    PRIMARY KEY (`id`),
    KEY `idx_locations_state` (`published`),
    KEY `idx_locations_created_by` (`created_by`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__sdajem_attendings`
(
    `id`            int(10) unsigned NOT NULL auto_increment,
    `access`        int(10) unsigned NOT NULL DEFAULT 0,
    `alias`         varchar(400),
    `state`         tinyint(3)       NOT NULL DEFAULT 0,
    `ordering`      int(11)          NOT NULL DEFAULT 0,
    `event_id`      INT UNSIGNED     NOT NULL COMMENT 'Foreign Key to #__yajame_events',
    `users_user_id` INT UNSIGNED     NULL COMMENT 'Foreign Key to #__users',
    `status`        TINYINT(1)       NOT NULL DEFAULT 0,
    `fittings`      varchar(50),
    `event_status`  tinyint(1),
    PRIMARY KEY (`id`),
    KEY `idx_attendings_event_id` (`event_id`),
    KEY `idx_attendings_user_id` (`users_user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__sdajem_fittings`
(
    `id`          int(10) unsigned NOT NULL auto_increment,
    `access`      int(10) unsigned NOT NULL DEFAULT 0,
    `alias`       varchar(400),
    `state`       tinyint(3)       NOT NULL DEFAULT 0,
    `ordering`    int(11)          NOT NULL DEFAULT 0,
    `title`       VARCHAR(255)     NOT NULL,
    `description` MEDIUMTEXT       NULL,
    `length`      DECIMAL(10, 2)   null,
    `width`       DECIMAL(10, 2)   null,
    `standard`    tinyint(1)       null     default 0,
    `fittingType` int(10) unsigned,
    `user_id`     int(10) unsigned,
    `image`       VARCHAR(1024)    null,
    `needSpace`   tinyint(1),
    PRIMARY KEY (`id`),
    KEY `idx_fittings_state` (`state`),
    KEY `idx_fittings_user_id` (`user_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  DEFAULT COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__sdajem_comments`
(
    `id`              INT UNSIGNED              NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
    `users_user_id`   INT UNSIGNED              NOT NULL COMMENT 'Foreign Key to #__users',
    `sdajem_event_id` INT UNSIGNED              NOT NULL COMMENT 'Foreign Key to #__yajame_events',
    `comment`         MEDIUMTEXT                NOT NULL,
    `timestamp`       DATETIME,
    `commentReadBy`   varchar(255) DEFAULT NULL NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_comment_time` (`timestamp` DESC)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    DEFAULT COLLATE = utf8mb4_unicode_ci;
