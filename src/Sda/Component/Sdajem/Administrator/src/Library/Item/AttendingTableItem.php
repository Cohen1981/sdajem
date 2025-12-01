<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Library\Item;

use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemInterface;
use Sda\Component\Sdajem\Administrator\Library\Trait\ItemTrait;
use stdClass;

/**
 * @package     Sda\Component\Sdajem\Administrator\Model\Item
 * @since       1.5.3
 * Representation of the database table #__sdajem_events.
 * All field types are database-compatible.
 */
class AttendingTableItem extends ItemClass
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
	 * @var integer|null
	 * @since 1.5.3
	 * Foreign key to the event table
	 */
	public ?int $event_id;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Foreign key to the Users table
	 */
	public ?int $users_user_id;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the status of an attendance, which can be translated and set with the IntAttStatusEnum.
	 */
	public ?int $status;

	/**
	 * @var string|null
	 * @since 1.5.3
	 * Stringified array of fittings
	 */
	public ?string $fittings;

	/**
	 * @var integer|null
	 * @since 1.5.3
	 * Represents the status of an event, which can be translated and set with the EventStatusEnum.
	 */
	public ?int $event_status;

	public static function getBaseQuery(QueryInterface $query, DatabaseInterface $db):QueryInterface
	{
		$query->select(
			$db->quoteName(
				[
					'a.id',
					'a.access',
					'a.alias',
					'a.state',
					'a.ordering',
					'a.event_id',
					'a.users_user_id',
					'a.status',
					'a.fittings',
					'a.event_status'
				]
			)
		);
		$query->from($db->quoteName('#__sdajem_attendings', 'a'));

		return $query;
	}
}
