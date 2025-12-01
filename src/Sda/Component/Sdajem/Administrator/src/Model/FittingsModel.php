<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use Sda\Component\Sdajem\Administrator\Library\Collection\FittingsCollection;
use Sda\Component\Sdajem\Administrator\Library\Collection\FittingTableItemsCollection;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\FittingTableItem;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Class FittingsModel
 * This class extends the ListModel and provides methods for managing and retrieving fittings-related data.
 *
 * @since 1.0.0
 */
class FittingsModel extends ListModel
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
				'a.id',
				'title',
				'a.title',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @since   1.0.0
	 * @return  QueryInterface
	 */
	protected function getListQuery(): QueryInterface
	{
		// Create a new query object.
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query = FittingTableItem::getBaseQuery($query, $db);

		// Join over User
		$query->select($db->quoteName('u.username', 'userName'))
			->join(
				'LEFT',
				$db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName('a.user_id')
			);

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
		$orderCol  = $this->state->get('list.ordering', 'a.title');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Retrieves a collection of items.
	 * @return FittingsCollection
	 * @since 1.5.3
	 */
	public function getItems(): FittingsCollection
	{
		return new FittingsCollection(parent::getItems());
	}

	/**
	 * @param   int|null $userId The unique identifier of the user whose fittings are to be retrieved.
	 *
	 * @return FittingTableItemsCollection
	 * @throws Exception
	 * @since 1.5.3
	 */
	public function getFittingsForUser(int $userId = null): FittingTableItemsCollection
	{
		$userId = !$userId ? Factory::getApplication()->getIdentity()->id : $userId;

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query = FittingTableItem::getBaseQuery($query, $db);

		$query->where($db->quoteName('a.user_id') . '= :userId');
		$query->bind(':userId', $userId);

		$db->setQuery($query);

		return new FittingTableItemsCollection($db->loadObjectList());
	}

	/**
	 * Get all fittings for a given event
	 *
	 * @since   1.1.4
	 *
	 * @param   int  $eventId  The event id
	 *
	 * @return  FittingsCollection|null
	 */
	public function getFittingsForEvent(int $eventId): ?FittingsCollection
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('att.fittings', 'fittings'));
		$query->from($db->quoteName('#__sdajem_attendings', 'att'));
		$query->where($db->quoteName('att.event_id') . '= :eventId');
		$query->extendWhere(
			'AND',
			'(' . $db->quoteName('att.status') . ' = ' . IntAttStatusEnum::POSITIVE->value . ')'
		);
		$query->bind(':eventId', $eventId);

		$db->setQuery($query);
		$fittings = $db->loadColumn();

		$pks = array();

		foreach ($fittings as $i => $value)
		{
			if (!empty($value))
			{
				$ids = json_decode($value, true);

				if (is_array($ids))
				{
					$pks = array_merge($pks, $ids);
				}
			}
		}

		if (count($pks) > 0)
		{
			$query = $db->getQuery(true);

			$query = FittingTableItem::getBaseQuery($query, $db);
			$query->select($db->quoteName('u.username', 'userName'))
				->join(
					'LEFT',
					$db->quoteName('#__users', 'u') . ' ON ' . $db->quoteName('u.id') . ' = ' . $db->quoteName(
						'a.user_id'
					)
				);
			$query->where($db->quoteName('a.id') . ' IN (' . implode(',', ArrayHelper::toInteger($pks)) . ')');
			$db->setQuery($query);
			$data = $db->loadObjectList();
		}
		else
		{
			$data = [];
		}

		return new FittingsCollection($data);
	}
}
