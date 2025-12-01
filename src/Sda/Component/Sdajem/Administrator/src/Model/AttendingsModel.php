<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Collection\AttendingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
use function defined;

defined('_JEXEC') or die();

/**
 * AttendingsModel is a model class responsible for managing attendings data.
 * This class extends ListModel and provides methods for interacting with the
 * attendees of events in the database, including retrieving, filtering, and
 * querying attendings data.
 *
 * @since 1.0.0
 */
class AttendingsModel extends ListModel
{
	/**
	 * Constructor.
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @since    1.0.0
	 * @throws \Exception
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'event_id', 'a.event_id',
				'eventTitle', 'e.title',
				'users_user_id', 'a.users_user_id',
				'attendeeName', 'at.username',
				'Status', 'a.Status',
				'startDateTime', 'e.startDateTime',
				'endDateTime', 'e.endDateTime'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  QueryInterface
	 *
	 * @throws \Exception
	 * @since   1.0.0
	 */
	protected function getListQuery(): QueryInterface
	{
		$currentUser = Factory::getApplication()->getIdentity();

		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query = Attending::getBaseQuery($query, $db);

		// Filter on user. Default Current User
		if ($this->getState('filter.users_user_id'))
		{
			if ($user = $this->getState('filter.users_user_id'))
			{
				$query->where($db->quoteName('a.users_user_id') . ' = ' . $db->quote($user));
			}
		}
		else
		{
			$query->where($db->quoteName('a.users_user_id') . ' = ' . $db->quote($currentUser->id));
		}

		// Filter on event
		if ($event = $this->getState('filter.event_id'))
		{
			$query->where($db->quoteName('a.event_id') . ' = ' . $db->quote($event));
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where(
					'(' . $db->quoteName('e.title') . ' LIKE ' . $search . ')'
				);
				$query->extendWhere('OR', '(' . $db->quoteName('at.username') . ' LIKE ' . $search . ')');
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'e.startDateTime');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * @param   int|null  $eventId The event id
	 *
	 * @return mixed
	 *
	 * @since 1.0.8
	 */
	public function getAttendingIdsToEvent(int $eventId = null)
	{
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($db->quoteName('a.id'));

		$query->from($db->quoteName('#__sdajem_attendings', 'a'));

		$query->where($db->quoteName('a.event_id') . '= :eventId');

		$query->bind(':eventId', $eventId);

		$db->setQuery($query);
		$data = $db->loadColumn();

		return $data;
	}

	/**
	 * Retrieves a collection of event attendings for a specific user.
	 *
	 * @param   int  $userId  The unique identifier of the user whose attendings are to be retrieved.
	 *
	 * @return AttendingsCollection A collection of attending records for the specified user.
	 * @since 1.5.3
	 */
	public function getAttendingsForUser(int $userId):AttendingsCollection
	{
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query = Attending::getBaseQuery($query, $db);

		$query->where($db->quoteName('a.users_user_id') . '= :userId');
		$query->bind(':userId', $userId);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return new AttendingsCollection($data);
	}

	/**
	 * Retrieves a collection of attendings for a specific event.
	 *
	 * @param   int|null  $eventId  The unique identifier of the event whose attendings are to be retrieved.
	 *                              If null, the method may handle it as retrieving attendings without a specific event context.
	 *
	 * @return AttendingsCollection A collection of attending records for the specified event.
	 * @since 1.5.3
	 */
	public function getAttendingsToEvent(int $eventId = null):AttendingsCollection
	{
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query = Attending::getBaseQuery($query, $db);

		$query->where($db->quoteName('a.event_id') . '= :eventId');
		$query->bind(':eventId', $eventId);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return new AttendingsCollection($data);
	}

	/**
	 * Retrieves a collection of items as an AttendingsCollection instance.
	 *
	 * @return AttendingsCollection A collection of items encapsulated as an AttendingsCollection object.
	 * @since 1.5.3
	 */
	public function getItems(): AttendingsCollection
	{
		return new AttendingsCollection(parent::getItems());
	}
}
