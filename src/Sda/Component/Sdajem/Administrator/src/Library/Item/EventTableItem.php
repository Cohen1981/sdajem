<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;
use ReflectionObject;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemInterface;
use Sda\Component\Sdajem\Administrator\Library\Trait\ItemTrait;
use stdClass;

/**
 * @package     Sda\Component\Sdajem\Administrator\Model\Item
 * @since       1.5.3
 * Representation of the database table #__sdajem_events.
 * All field types are database-compatible.
 */
class EventTableItem extends ItemClass
{
	/**
	 * @var integer|null
	 * @since 1.5.3
	 * The primary Key of the table
	 */
	public ?int $id;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Stores the joomla access level
	 */
	public ?int $access;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * The joomla alias for an item
	 */
	public ?string $alias;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Creation datetime of this item
	 */
	public ?string $created;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * User id of creator
	 */
	public ?int $created_by;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Joomla publishing state
	 */
	public ?int $published;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Stores the publish-up date and time in string format
	 */
	public ?string $publish_up;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the date and time when the content will stop being published.
	 */
	public ?string $publish_down;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the state or status of an entity.
	 */
	public ?int $state;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Specifies the ordering of items.
	 */
	public ?int $ordering;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Stores the event title
	 */
	public ?string $title;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Holds the description or details of the event.
	 */
	public ?string $description;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Stores the external URL to the event as a string.
	 */
	public ?string $url;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Holds the URL or path to the image resource
	 */
	public ?string $image;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the unique identifier for the sdajem location.
	 */
	public ?int $sdajem_location_id;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the identifier of the host. Foreign key to #__contact_details
	 */
	public ?int $hostId;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the unique identifier for the organizer. Foreign key to #__users
	 */
	public ?int $organizerId;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the start date and time.
	 */
	public ?string $startDateTime;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Represents the end date and time.
	 */
	public ?string $endDateTime;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Indicates if the event is an all-day event. 1 if true
	 */
	public ?int $allDayEvent;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the status of an event, which can be translated and set with the EventStatusEnum.
	 */
	public ?int $eventStatus;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Indicates whether the event has been cancelled. Cancelled events will be shown on ListView.
	 */
	public ?int $eventCancelled;

	/**
	 * @var array|null
	 * @since 1.5.3
	 * Holds configuration parameters for the event. For example, if users can register for the event.
	 */
	public array|string|null $params;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Holds the SVG content as a string or null if not set
	 */
	public ?string $svg;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Stores the registration expiration time or date
	 */
	public ?string $registerUntil;

	/**
	 * Retrieves the start date and time of an event.
	 *
	 * @return string The formatted start date and time. The format is 'd-m-Y' if it is an all-day event
	 *                and 'd-m-Y H:i' otherwise.
	 * @since 1.5.3
	 */
	public function getStart(): string
	{
		if ($this->allDayEvent)
		{
			return HTMLHelper::date($this->startDateTime, 'd.m.Y');
		}
		else
		{
			return HTMLHelper::date($this->startDateTime, 'd.m.Y H:i');
		}
	}

	/**
	 * Retrieves the end date and time of an event.
	 *
	 * @return string The formatted end date and time. The format is 'd-m-Y' if it is an all-day event
	 *                and 'd-m-Y H:i' otherwise.
	 * @since 1.5.3
	 */
	public function getEnd(): string
	{
		if ($this->allDayEvent)
		{
			return HTMLHelper::date($this->endDateTime, 'd.m.Y');
		}
		else
		{
			return HTMLHelper::date($this->endDateTime, 'd.m.Y H:i');
		}
	}
}
