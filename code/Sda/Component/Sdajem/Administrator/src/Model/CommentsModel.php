<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Collection\CommentsCollection;
use Sda\Component\Sdajem\Administrator\Library\Collection\CommentTableItemsCollection;
use Sda\Component\Sdajem\Administrator\Library\Item\CommentTableItem;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Model class to manage comments in the Sdajem component.
 * This class provides functionality to load, filter, and query comments
 * associated with events and users in the system. It inherits from Joomla's
 * `ListModel`.
 *
 * @since 1.0.0
 */
class CommentsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @since    1.0.0
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @throws \Exception
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'c.id',
				'sdajem_event_id',
				'c.event_id',
				'users_user_id',
				'c.users_user_id',
				'comment',
				'c.comment',
				'timestamp',
				'c.timestamp',
				'commentReadBy',
				'c.commentReadBy'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @since   1.0.0
	 * @return  QueryInterface
	 * @throws \Exception
	 */
	protected function getListQuery(): QueryInterface
	{
		$currentUser = Factory::getApplication()->getIdentity();

		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query = CommentTableItem::getBaseQuery($query, $db);

		// Filter on user. Default Current User
		if ($this->getState('filter.users_user_id'))
		{
			if ($user = $this->getState('filter.users_user_id'))
			{
				$query->where($db->quoteName('c.users_user_id') . ' = ' . $db->quote($user));
			}
		}
		else
		{
			$query->where($db->quoteName('c.users_user_id') . ' = ' . $db->quote($currentUser->id));
		}

		// Filter on event
		if ($event = $this->getState('filter.event_id'))
		{
			$query->where($db->quoteName('c.sdajem_event_id') . ' = ' . $db->quote($event));
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('c.id = ' . (int) substr($search, 3));
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'e.startDateTime');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Retrieves a collection of comment table items.
	 *
	 * @since 1.5.3
	 * @return CommentTableItemsCollection A collection of comment table items.
	 * @throws \Exception
	 */
	public function getItems(): CommentTableItemsCollection
	{
		return new CommentTableItemsCollection(parent::getItems());
	}

	/**
	 * Retrieves the IDs of comments associated with a specific event.
	 *
	 * @param   int|null  $eventId  The ID of the event for which the comment IDs are to be fetched. Defaults to null.
	 *
	 * @return array An array of comment IDs related to the specified event.
	 * @since 1.5.3
	 */
	public function getCommentIdsToEvent(int $eventId = null): array
	{
		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($db->quoteName('a.id'));

		$query->from($db->quoteName('#__sdajem_comments', 'a'));

		$query->where($db->quoteName('a.sdajem_event_id') . '=' . $eventId);

		$db->setQuery($query);

		return ($db->loadColumn() ?? []);
	}

	/**
	 * Retrieves comments associated with a specific event.
	 *
	 * @param   int|null  $eventId  The ID of the event for which the comments are to be fetched. Defaults to null.
	 *
	 * @return CommentsCollection A collection of comments related to the specified event.
	 * @since 1.5.3
	 */
	public function getCommentsToEvent(int $eventId = null): CommentsCollection
	{
		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query = CommentTableItem::getBaseQuery($query, $db);

		$query->where($db->quoteName('c.sdajem_event_id') . '= :eventId');
		$query->order($db->quoteName('c.timestamp') . ' DESC');
		$query->bind(':eventId', $eventId);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return new CommentsCollection($data);
	}
}
