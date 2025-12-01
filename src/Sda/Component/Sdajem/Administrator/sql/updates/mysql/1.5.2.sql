alter table `#__sdajem_attendings`
    add column `event_status` tinyint(1);

UPDATE `#__sdajem_attendings`
SET event_status = 0;

INSERT INTO `#__sdajem_attendings` (access,
                                    alias,
                                    state,
                                    ordering,
                                    event_id,
                                    users_user_id,
                                    status,
                                    fittings,
                                    event_status)
        SELECT access,
               alias,
               state,
               ordering,
               event_id,
               users_user_id,
               status,
               NULL,
               4

        FROM `#__sdajem_interest`;

DROP TABLE `#__sdajem_interest`;