update `#__sdajem_events` set `catid` = null;
update `#__sdajem_locations` set `catid` = null;
delete from `#__assets` WHERE `name` LIKE 'com_sdajem.locations%';
delete from `#__categories` WHERE `extension` LIKE 'com_sdajem%';