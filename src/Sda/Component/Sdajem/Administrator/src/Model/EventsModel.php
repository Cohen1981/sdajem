<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Collection\EventsCollection;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\Event;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Administrator\Model
 * @since       1.0.1
 */
class EventsModel extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @since 1.0.1
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
				'a.id',
				'title',
				'a.title',
				'published',
				'a.published',
				'access',
				'a.access',
				'access_level',
				'ordering',
				'a.ordering',
				'publish_up',
				'a.publish_up',
				'publish_down',
				'a.publish_down',
				'startDateTime',
				'a.startDateTime',
				'endDateTime',
				'a.endDateTime',
				'eventStatus',
				'a.eventStatus',
				'attendeeCount'
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
		$params = ComponentHelper::getParams('com_sdajem');

		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query = Event::getBaseQuery($query, $db);

		// Filter by access level.
		if ($this->getState('filter.access', true))
		{
			$groups = $this->getState(
				'filter.viewlevels',
				Factory::getApplication()->getIdentity()->getAuthorisedViewLevels()
			);
			$query->whereIn($db->quoteName('a.access'), $groups);
		}

		// Filter by published state
		$published = (string) $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where($db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(' . $db->quoteName('a.published') . ' = 0 OR ' . $db->quoteName('a.published') . ' = 1)');
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
					'(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')'
				);
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'a.startDateTime');
		$orderDirn = $this->state->get('list.direction', 'asc');

		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = $db->quoteName('c.title') . ' ' . $orderDirn . ', ' . $db->quoteName('a.ordering');
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @since 1.0.1
	 * @return EventsCollection the collection of items.
	 * A collection of items on success, or false on failure.
	 */
	public function getItems(): EventsCollection
	{
		return new EventsCollection(parent::getItems());
	}
}
