<?php
/**
 * @package     Sda\Component\Sdajem\Administrator\Library\Item
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */
namespace Sda\Component\Sdajem\Administrator\Library\Item;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;

/**
 * @package     Sda\Component\Sdajem\Administrator\Library\Item
 *
 * @since       version 1.5.3
 */
class Attending extends AttendingTableItem
{
	/**
	 * @var string|null
	 * @since version 1.5.3
	 */
	public ?string $eventTitle;

	/**
	 * @var string|null
	 * @since version 2.0.0
	 */
	public ?string $eventAlias = '';

	/**
	 * @var int|null
	 * @since version 1.5.3
	 */
	public ?int $eventStatus;

	/**
	 * @var string|null
	 * @since version 1.5.3
	 */
	public ?string $startDateTime;

	/**
	 * @var string|null
	 * @since version 1.5.3
	 */
	public ?string $endDateTime;
	/**
	 * @var string|null
	 * @since version 1.5.3
	 */
	public ?string $attendeeName;

	/**
	 * @var array|null
	 * @since version  1.5.3
	 */
	public ?array $fittingItems;

	/**
	 * @var IntAttStatusEnum|null
	 * The user attending status to the event
	 * @since version 1.5.3
	 */
	public ?IntAttStatusEnum $statusEnum;

	/**
	 * @param   array  $data The item data as an array
	 *
	 * @return $this
	 *
	 * @since 1.5.3
	 */
	public static function createFromArray(array $data = []): static
	{
		if (empty($data))
		{
			$item = new static;
		}
		else
		{
			$item = parent::createFromArray($data);
		}

		if (isset($item->fittings))
		{
			$item->fittingItems = json_decode($item->fittings, true);
		}

		$item->statusEnum = ($item->status) ? IntAttStatusEnum::from($item->status) : IntAttStatusEnum::NA;

		return $item;
	}

	public static function getBaseQuery(QueryInterface $query, DatabaseInterface $db):QueryInterface
	{
		$query = parent::getBaseQuery($query, $db);

		// Join event
		$query->select($db->quoteName('e.title', 'eventTitle'))
			->select($db->quoteName('e.alias', 'eventAlias'))
			->select($db->quoteName('e.eventStatus', 'eventStatus'))
			->select($db->quoteName('e.startDateTime', 'startDateTime'))
			->select($db->quoteName('e.endDateTime', 'endDateTime'))
			->join(
				'LEFT',
				$db->quoteName('#__sdajem_events', 'e') . ' ON ' . $db->quoteName('e.id') . '=' . $db->quoteName('a.event_id')
			);

		// Join over User as attendee
		$query->select($db->quoteName('at.username', 'attendeeName'))
			->join(
				'LEFT',
				$db->quoteName('#__users', 'at') . ' ON ' . $db->quoteName('at.id') . ' = ' . $db->quoteName('a.users_user_id')
			);

		return $query;
	}
}
