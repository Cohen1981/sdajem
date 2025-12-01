<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Model;

use Sda\Component\Sdajem\Administrator\Library\Collection\CommentsCollection;

defined('_JEXEC') or die();

/**
 * Comments model for the Joomla Events component.
 * @since 1.5.3
 */
class CommentsModel extends \Sda\Component\Sdajem\Administrator\Model\CommentsModel
{
	/**
	 * Retrieves comments associated with a specific event.
	 *
	 * @param   int|null  $eventId  The ID of the event for which the comments are to be fetched. Defaults to null.
	 *
	 * @return CommentsCollection A collection of comments related to the specified event.
	 * @since 1.5.3
	 */
	public function getCommentsToEvent(int $eventId = null):CommentsCollection
	{
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				[
					$db->quoteName('a.id'),
					$db->quoteName('a.sdajem_event_id'),
					$db->quoteName('a.users_user_id'),
					$db->quoteName('a.comment'),
					$db->quoteName('a.timestamp'),
					$db->quoteName('a.commentReadBy'),
				]
			)
		);
		$query->from($db->quoteName('#__sdajem_comments', 'a'));

		$query->where($db->quoteName('a.sdajem_event_id') . '= :eventId');
		$query->order($db->quoteName('a.timestamp') . ' DESC');
		$query->bind(':eventId', $eventId);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return new CommentsCollection($data);
	}

	/**
	 * Retrieves the IDs of comments associated with a specific event.
	 *
	 * @param   int|null  $eventId  The ID of the event for which the comment IDs are to be fetched. Defaults to null.
	 *
	 * @return array An array of comment IDs related to the specified event.
	 * @since 1.5.3
	 */
	public function getCommentIdsToEvent(int $eventId = null):array
	{
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($db->quoteName('a.id'));

		$query->from($db->quoteName('#__sdajem_comments', 'a'));

		$query->where($db->quoteName('a.sdajem_event_id') . '=' . $eventId);

		$db->setQuery($query);
		$data = ($db->loadColumn() ?? []);

		return $data;
	}
}
