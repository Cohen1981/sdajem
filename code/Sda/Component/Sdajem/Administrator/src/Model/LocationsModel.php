<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Collection\LocationsCollection;

/**
 * The LocationsModel class provides methods for querying and managing
 * a list of location-related data within a Joomla! application. It extends the
 * ListModel class, inheriting its functionalities while introducing additional
 * filtering, sorting, and state management related to locations.
 * @since 1.0.0
 */
class LocationsModel extends ListModel
{
	/**
	 * Constructor.
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @since   1.0.0
	 * @throws \Exception
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'postalCode', 'a.postalCode'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  QueryInterface
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
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
					$db->quoteName('a.access'),
					$db->quoteName('a.alias'),
					$db->quoteName('a.created'),
					$db->quoteName('a.created_by'),
					$db->quoteName('a.published'),
					$db->quoteName('a.publish_up'),
					$db->quoteName('a.publish_down'),
					$db->quoteName('a.state'),
					$db->quoteName('a.ordering'),
					$db->quoteName('a.title'),
					$db->quoteName('a.description'),
					$db->quoteName('a.url'),
					$db->quoteName('a.street'),
					$db->quoteName('a.postalCode'),
					$db->quoteName('a.city'),
					$db->quoteName('a.stateAddress'),
					$db->quoteName('a.country'),
					$db->quoteName('a.latlng'),
					$db->quoteName('a.contactId'),
					$db->quoteName('a.image'),
				]
			)
		);
		$query->from($db->quoteName('#__sdajem_locations', 'a'));

		// Join over the asset groups.
		$query->select($db->quoteName('ag.title', 'accessLevel'))
			->join(
				'LEFT',
				$db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
			);

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where($db->quoteName('a.access') . ' = ' . (int) $access);
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
		$orderCol = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'asc');

		if ($orderCol == 'a.ordering')
		{
			$orderCol = $db->quoteName('c.title') . ' ' . $orderDirn . ', ' . $db->quoteName('a.ordering');
		}

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Populates the state object with ordering, direction, and additional parameters.
	 *
	 * @param   string  $ordering   The field to order by.
	 * @param   string  $direction  The direction to order by (asc or desc).
	 *
	 * @return  void
	 * @since   1.0.0
	 * @throws \Exception
	 */
	protected function populateState($ordering = 'a.title', $direction = 'asc')
	{
		$app = Factory::getApplication();
		$forcedLanguage = $app->input->get('forcedLanguage', '', 'cmd');

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Adjust the context to support forced languages.
		if ($forcedLanguage)
		{
			$this->context .= '.' . $forcedLanguage;
		}

		// List state information.
		parent::populateState($ordering, $direction);

		// Force a language.
		if (!empty($forcedLanguage))
		{
			$this->setState('filter.language', $forcedLanguage);
		}
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @return LocationsCollection A collection of location table items.
	 * @since 1.0.0
	 */
	public function getItems():LocationsCollection
	{
		return new LocationsCollection(parent::getItems());
	}
}
