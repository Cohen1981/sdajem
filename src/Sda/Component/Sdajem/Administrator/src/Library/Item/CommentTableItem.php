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
class CommentTableItem extends ItemClass
{
	/**
	 * @var integer|null
	 * @since 1.5.3
	 * The primary Key of the table
	 */
	public ?int $id;

	/**
	 * @var integer|null
	 * Represents the unique identifier for a user.
	 * @since 1.5.3
	 */
	public ?int $users_user_id;

	/**
	 * @var integer|null
	 * Holds the unique identifier for the sdajem event
	 * @since 1.5.3
	 */
	public ?int $sdajem_event_id;

	/**
	 * @var string|null
	 * Holds the comment text
	 * @since 1.5.3
	 */
	public ?string $comment;

	/**
	 * @var string|null
	 * Stores the timestamp value.
	 * @since 1.5.3
	 */
	public ?string $timestamp;

	/**
	 * @var string|null
	 * Stores the read status of the comment.
	 * @since 1.5.3
	 */
	public ?string $commentReadBy;

	/**
	 * Constructs and returns a base query for retrieving event data from the database.
	 * For extending the query us c. as referer to the comment table.
	 *
	 * @param   QueryInterface     $query  The query object to build upon.
	 * @param   DatabaseInterface  $db     The database connection object.
	 *
	 * @return \JDatabaseQuery          The modified query object with the constructed query.
	 * @since 1.5.3
	 */
	public static function getBaseQuery(QueryInterface $query, DatabaseInterface $db): QueryInterface
	{
		$query->select(
			$db->quoteName(
				[
					'c.id',
					'c.sdajem_event_id',
					'c.users_user_id',
					'c.comment',
					'c.timestamp',
					'c.commentReadBy',
				]
			)
		);
		$query->from($db->quoteName('#__sdajem_comments', 'c'));

		return $query;
	}
}
