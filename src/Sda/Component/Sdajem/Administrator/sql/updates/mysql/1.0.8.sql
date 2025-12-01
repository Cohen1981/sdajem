drop table if exists `#__sdajem_fittingtypes`;

alter table `#__sdajem_fittings`
add column `image` VARCHAR(1024);

alter table `#__sdajem_fittings`
add column `needSpace` tinyint(1);