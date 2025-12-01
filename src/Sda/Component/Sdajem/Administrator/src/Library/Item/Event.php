<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;
use Joomla\Registry\Registry;
use ReflectionObject;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Trait\ItemTrait;
use stdClass;

/**
 * @package     Sda\Component\Sdajem\Site\Model\Item
 * @since       1.4.0
 * For programming convenience, the class gives type hinting for the class properties.
 * For frontend use there are additional fields with information from foreign tables
 */
class Event extends EventTableItem
{
	/**
	 * @var EventStatusEnum|null
	 * Enum for the event status. For convenience set at construction of this class.
	 * @since 1.4.0
	 */
	public ?EventStatusEnum $eventStatusEnum;

	/**
	 * @var Registry|null
	 * Registry for the event parameters. For convenience set at construction of this class.
	 * @since 1.4.0
	 */
	public ?Registry $paramsRegistry;

	/**
	 * @var array|null
	 * Array of SVGs for the event. For convenience set at construction of this class.
	 * @since 1.4.0
	 */
	public ?array $svgArray;

	/**
	 * @var string|null
	 * The location name of the event. For convenience set at construction of this class.
	 * @since 1.4.0
	 */
	public ?string $locationName;

	/**
	 * @var string|null
	 * postalCode
	 * The postal code of the event location.
	 * @since 1.5.3
	 */
	public ?string $postalCode;

	/**
	 * @var string|null
	 * organizerName
	 * The name of the event organizer.
	 * @since 1.5.3
	 */
	public ?string $organizerName;

	/**
	 * @var integer|null
	 * attendeeCount
	 * The number of attendees for the event.
	 * @since 1.5.3
	 */
	public ?int $attendeeCount;

	/**
	 * @var integer|null
	 * guestCount
	 * The number of guests for the event.
	 * @since 1.5.3
	 */
	public ?int $guestCount;

	/**
	 * @var integer|null
	 * attendeeFeedbackCount
	 * The number of feedbacks for the event.
	 * @since 1.5.3
	 */
	public ?int $attendeeFeedbackCount;

	/**
	 * @var integer|null
	 * interestCount
	 * The number of interests for the event.
	 * @since 1.5.3
	 */
	public ?int $interestCount;

	/**
	 * @var integer|null
	 * feedbackCount
	 * The number of feedbacks for the event.
	 * @since 1.5.3
	 */
	public ?int $feedbackCount;

	/**
	 * @var integer|null
	 * commentsCount
	 * The number of comments for the event.
	 * @since 1.5.3
	 */
	public ?int $commentsCount;

	/**
	 * @var string|null
	 * accessLevel
	 * The access level of the event.
	 * @since 1.5.3
	 */
	public ?string $accessLevel;

	/**
	 * @var array|null
	 * Array of SVGs for the event.
	 * @since 1.5.3
	 */
	public ?array $svgs;

	/**
	 * @since 1.4.0
	 *
	 * @param   array  $data  the event data as an array
	 *
	 * @return self
	 */
	public static function createFromArray(array $data = []): static
	{
		$item = parent::createFromArray($data);

		$item->eventStatusEnum       = EventStatusEnum::from($data['eventStatus']);
		$item->paramsRegistry        = new Registry($data['params']);
		$item->locationName          = (isset($data['locationName'])) ? $data['locationName'] : '';
		$item->postalCode            = (isset($data['postalCode'])) ? $data['postalCode'] : '';
		$item->organizerName         = (isset($data['organizerName'])) ? $data['organizerName'] : '';
		$item->attendeeCount         = $data['attendeeCount'];
		$item->guestCount            = $data['guestCount'];
		$item->attendeeFeedbackCount = $data['attendeeFeedbackCount'];
		$item->interestCount         = $data['interestCount'];
		$item->feedbackCount         = $data['feedbackCount'];
		$item->commentsCount = $data['commentsCount'];
		$item->svgs = (isset($data['svg'])) ? (array) json_decode($data['svg']) : [];

		return $item;
	}

	/**
	 * Constructs and returns a base query for retrieving event data from the database.
	 *
	 * @param   QueryInterface     $query  The query object to build upon.
	 * @param   DatabaseInterface  $db     The database connection object.
	 *
	 * @return \JDatabaseQuery          The modified query object with the constructed query.
	 * @since 1.5.3
	 */
	public static function getBaseQuery(QueryInterface $query,DatabaseInterface $db): QueryInterface
	{
		$query->select(
			$db->quoteName(
				[
					'a.id',
					'a.access',
					'a.alias',
					'a.created',
					'a.created_by',
					'a.published',
					'a.publish_up',
					'a.publish_down',
					'a.state',
					'a.ordering',
					'a.title',
					'a.description',
					'a.url',
					'a.startDateTime',
					'a.endDateTime',
					'a.allDayEvent',
					'a.sdajem_location_id',
					'a.image',
					'a.eventStatus',
					'a.organizerId',
					'a.registerUntil',
					'a.hostId',
					'a.eventCancelled',
					'a.params',
					'a.svg',
				]
			)
		);
		$query->from($db->quoteName('#__sdajem_events', 'a'));

		// Join over locations
		$query->select(
			$db->quoteName('loc.title', 'locationName'),
			$db->quoteName('loc.postalCode', 'postalCode')
		)
			->join(
				'LEFT',
				$db->quoteName('#__sdajem_locations', 'loc') . ' ON ' . $db->quoteName(
					'loc.id'
				) . ' = ' . $db->quoteName('a.sdajem_location_id')
			);

		// Join over User as organizer
		$query->select($db->quoteName('org.username', 'organizerName'))
			->join(
				'LEFT',
				$db->quoteName('#__users', 'org') . ' ON ' . $db->quoteName('org.id') . ' = ' . $db->quoteName(
					'a.organizerId'
				)
			);

		// Get the Attendee count
		$attendees = $db->getQuery(true)
			->select('COUNT(' . $db->quoteName('att.id') . ')')
			->from($db->quoteName('#__sdajem_attendings', 'att'))
			->where(
				[
					$db->quoteName('att.event_id') . ' = ' . $db->quoteName('a.id'),
					$db->quoteName('att.status') . ' = ' . IntAttStatusEnum::POSITIVE->value,
					$db->quoteName('att.event_status') . ' = ' . EventStatusEnum::OPEN->value
				]
			);
		$query->select('(' . $attendees . ') AS ' . $db->quoteName('attendeeCount'));

		// Get the guest count
		$guests = $db->getQuery(true)
			->select('COUNT(' . $db->quoteName('g.id') . ')')
			->from($db->quoteName('#__sdajem_attendings', 'g'))
			->where(
				[
					$db->quoteName('g.event_id') . ' = ' . $db->quoteName('a.id'),
					$db->quoteName('g.status') . ' = ' . IntAttStatusEnum::GUEST->value,
					$db->quoteName('g.event_status') . ' = ' . EventStatusEnum::OPEN->value
				]
			);
		$query->select('(' . $guests . ') AS ' . $db->quoteName('guestCount'));

		// Get the Attendee feedback count
		$attendeesf = $db->getQuery(true)
			->select('COUNT(' . $db->quoteName('atte.id') . ')')
			->from($db->quoteName('#__sdajem_attendings', 'atte'))
			->where(
				[
					$db->quoteName('atte.event_id') . ' = ' . $db->quoteName('a.id'),
					$db->quoteName(
						'atte.status'
					) . ' IN( ' . IntAttStatusEnum::POSITIVE->value . ',
							 ' . IntAttStatusEnum::NEGATIVE->value . ',
							  ' . IntAttStatusEnum::GUEST->value . ')',
					$db->quoteName('atte.event_status') . ' = ' . EventStatusEnum::OPEN->value
				]
			);
		$query->select('(' . $attendeesf . ') AS ' . $db->quoteName('attendeeFeedbackCount'));

		$interestCount = $db->getQuery(true)
			->select('COUNT(' . $db->quoteName('int.id') . ')')
			->from($db->quoteName('#__sdajem_attendings', 'int'))
			->where(
				[
					$db->quoteName('int.event_id') . ' = ' . $db->quoteName('a.id'),
					$db->quoteName('int.status') . ' = ' . IntAttStatusEnum::POSITIVE->value,
					$db->quoteName('int.event_status') . ' = ' . EventStatusEnum::PLANING->value
				]
			);
		$query->select('(' . $interestCount . ') AS ' . $db->quoteName('interestCount'));

		$feedback = $db->getQuery(true)
			->select('COUNT(' . $db->quoteName('i.id') . ')')
			->from($db->quoteName('#__sdajem_attendings', 'i'))
			->where(
				[
					$db->quoteName('i.event_id') . ' = ' . $db->quoteName('a.id'),
					$db->quoteName('i.event_status') . ' = ' . EventStatusEnum::PLANING->value
				]
			);
		$query->select('(' . $feedback . ') AS ' . $db->quoteName('feedbackCount'));

		$commentsCount = $db->getQuery(true)
			->select('COUNT(' . $db->quoteName('com.id') . ')')
			->from($db->quoteName('#__sdajem_comments', 'com'))
			->where($db->quoteName('com.sdajem_event_id') . ' = ' . $db->quoteName('a.id'));
		$query->select('(' . $commentsCount . ') AS ' . $db->quoteName('commentsCount'));

		// Join over the asset groups.
		$query->select($db->quoteName('ag.title', 'accessLevel'))
			->join(
				'LEFT',
				$db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName(
					'ag.id'
				) . ' = ' . $db->quoteName('a.access')
			);

		return $query;
	}
}
