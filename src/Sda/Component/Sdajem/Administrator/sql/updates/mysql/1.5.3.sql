/*
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

alter table `#__sdajem_events`
    drop column `created_by_alias`;

alter table `#__sdajem_events`
    drop column `language`;

alter table `#__sdajem_events`
    drop column `catid`;

alter table `#__sdajem_locations`
    drop column `created_by_alias`;

alter table `#__sdajem_locations`
    drop column `language`;

alter table `#__sdajem_locations`
    drop column `catid`;
