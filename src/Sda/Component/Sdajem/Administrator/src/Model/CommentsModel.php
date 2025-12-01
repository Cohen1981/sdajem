<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Collection\CommentTableItemsCollection;
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
	protected function getListQuery()
	{
		$currentUser = Factory::getApplication()->getIdentity();

		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				[
					$db->quoteName('c.id'),
					$db->quoteName('c.sdajem_event_id'),
					$db->quoteName('c.users_user_id'),
					$db->quoteName('c.comment'),
					$db->quoteName('c.timestamp'),
					$db->quoteName('c.commentReadBy'),
				]
			)
		);
		$query->from($db->quoteName('#__sdajem_comments', 'c'));

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
}
