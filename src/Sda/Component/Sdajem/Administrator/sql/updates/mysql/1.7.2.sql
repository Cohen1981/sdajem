update `#__sdajem_locations`
set `alias` = REPLACE(CONCAT(LOWER(`title`), '-', COALESCE(`postalCode`, '')), ' ', '-');

update `#__sdajem_events`
set `alias` = REPLACE(CONCAT(LOWER(`title`), '-', DATE_FORMAT(`startDateTime`, '%d.%m.%Y')), ' ', '-');

update `#__sdajem_fittings`
set `alias` =
        REPLACE(
                CONCAT(
                        (SELECT `username`
                         FROM `#__users`
                         WHERE `id` = `user_id`)
                    , '-', LOWER(`title`)
                    , '-', `length`, '-', `width`
                ), ' ', '-'
        )
;

update `#__sdajem_attendings`
set `alias` =
        REPLACE(
                CONCAT(
                        (SELECT `username`
                         FROM `#__users`
                         WHERE `id` = `users_user_id`)
                    , '-', LOWER(
                                (SELECT CONCAT(`title`, '-', DATE_FORMAT(`startDateTime`, '%d.%m.%Y'))
                                 FROM `#__sdajem_events`
                                 WHERE `id` = `event_id`)
                           )
                ), ' ', '-'
        )
;
