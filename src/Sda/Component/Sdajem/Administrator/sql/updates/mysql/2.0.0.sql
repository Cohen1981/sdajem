alter table `#__sdajem_attendings`
    add constraint `sdajem_attendings_sdajem_events_id_fk`
        foreign key (`event_id`) references `#__sdajem_events` (`id`)
            on delete cascade;

alter table `#__sdajem_attendings`
    modify `users_user_id` INT null comment 'Foreign Key to #__users';

alter table `#__sdajem_attendings`
    add constraint `sdajem_attendings_users_id_fk`
        foreign key (`users_user_id`) references `#__users` (`id`)
            on delete cascade;

alter table `#__sdajem_comments`
    modify `users_user_id` INT not null comment 'Foreign Key to #__users';

alter table `#__sdajem_comments`
    add constraint `sdajem_comments_sdajem_events_id_fk`
        foreign key (`sdajem_event_id`) references `#__sdajem_events` (`id`)
            on delete cascade;

alter table `#__sdajem_comments`
    add constraint `sdajem_comments_users_id_fk`
        foreign key (`users_user_id`) references `#__users` (`id`)
            on delete cascade;

alter table `#__sdajem_events`
    modify `created_by` INT null;

alter table `#__sdajem_events`
    add constraint `sdajem_events_contact_details_id_fk`
        foreign key (`hostId`) references `#__contact_details` (`id`)
            on delete set null;

alter table `#__sdajem_events`
    add constraint `sdajem_events_sdajem_locations_id_fk`
        foreign key (`sdajem_location_id`) references `#__sdajem_locations` (`id`)
            on delete set null;

alter table `#__sdajem_events`
    add constraint `sdajem_events_users_id_fk`
        foreign key (`created_by`) references `#__users` (`id`)
            on delete set null;

alter table `#__sdajem_events`
    add constraint `sdajem_events_users_id_fk_2`
        foreign key (`organizerId`) references `#__users` (`id`)
            on delete set null;

alter table `#__sdajem_fittings`
    modify `user_id` INT null;

alter table `#__sdajem_fittings`
    add constraint `sdajem_fittings_users_id_fk`
        foreign key (`user_id`) references `#__users` (`id`)
            on delete cascade;

alter table `#__sdajem_locations`
    add constraint `sdajem_locations_contact_details_id_fk`
        foreign key (`contactId`) references `#__contact_details` (`id`)
            on delete set null;