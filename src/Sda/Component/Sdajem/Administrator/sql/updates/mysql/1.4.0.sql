 alter table `#__sdajem_events`
     drop column `checked_out`;

 alter table `#__sdajem_events`
     drop column `checked_out_time`;

 alter table `#__sdajem_locations`
     drop column `checked_out_time`;

 alter table `#__sdajem_locations`
     drop column `checked_out_time`;

 alter table `#__sdajem_attendings`
     drop column `created`;

 alter table `#__sdajem_attendings`
     drop column `created_by`;

 alter table `#__sdajem_attendings`
     drop column `created_by_alias`;

 alter table `#__sdajem_fittings`
     drop column `created`;

 alter table `#__sdajem_fittings`
     drop column `created_by`;

 alter table `#__sdajem_fittings`
     drop column `created_by_alias`;

 alter table `#__sdajem_fittings`
     drop column `published_up`;

 alter table `#__sdajem_fittings`
     drop column `published_down`;

 alter table `#__sdajem_fittings`
     drop column `published`;

 alter table `#__sdajem_interest`
     drop column `created`;

 alter table `#__sdajem_interest`
     drop column `created_by`;

 alter table `#__sdajem_interest`
     drop column `created_by_alias`;