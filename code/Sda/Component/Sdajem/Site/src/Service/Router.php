<?php
/**
 * @package        Sda\Component\Sdajem\Site\Router
 * @subpackage     com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * @since          1.7.2
 */

namespace Sda\Component\Sdajem\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class Router extends RouterView
{
	private DatabaseInterface $db;

	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$this->db = $db;

		// Register the event views
		$events = new RouterViewConfiguration('events');
		$this->registerView($events);

		$event = new RouterViewConfiguration('event');
		$event->setKey('id');
		$event->setParent($events, 'Itemid');
		$this->registerView($event);

		$eventform = new RouterViewConfiguration('eventform');
		$eventform->setKey('id');
		$this->registerView($eventform);

		// Register the location views
		$locations = new RouterViewConfiguration('locations');
		$this->registerView($locations);

		$location = new RouterViewConfiguration('location');
		$location->setKey('id');
		$location->setParent($locations, 'Itemid');
		$this->registerView($location);

		$locationform = new RouterViewConfiguration('locationform');
		$locationform->setKey('id');
		$this->registerView($locationform);

		// Register the attending views
		$attendings = new RouterViewConfiguration('attendings');
		$this->registerView($attendings);

		$attending = new RouterViewConfiguration('attending');
		$attending->setKey('id');
		$attending->setParent($attendings, 'Itemid');
		$this->registerView($attending);

		$attendingform = new RouterViewConfiguration('attendingform');
		$attendingform->setKey('id');
		$this->registerView($attendingform);

		// Register the fitting views
		$fittings = new RouterViewConfiguration('fittings');
		$this->registerView($fittings);

		$fitting = new RouterViewConfiguration('fitting');
		$fitting->setKey('id');
		$fitting->setParent($fittings, 'Itemid');
		$this->registerView($fitting);

		$fittingform = new RouterViewConfiguration('fittingform');
		$fittingform->setKey('id');
		$this->registerView($fittingform);

		$commentform = new RouterViewConfiguration('commentform');
		$commentform->setKey('id');
		$this->registerView($commentform);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return array
	 * @since 1.7.2
	 */
	private function getSegment(string $id, array $query): array
	{
		if (strpos($id, ':'))
		{
			[$void, $segment] = explode(':', $id, 2);

			return [$void => $segment];
		}

		return [(int) $id => $id];
	}

	/**
	 * Retrieves the ID associated with the given segment or query.
	 *
	 * This method attempts to resolve an ID based on a segment parameter
	 * and a table name. If the segment is directly an ID, it validates
	 * and retrieves the associated alias to ensure consistency with the routing.
	 *
	 * @param   mixed   $segment    The segment parameter, which could be an alias or an ID-like value.
	 * @param   mixed   $query      Not explicitly used in this function but passed for context or possible future implementation.
	 * @param   string  $tableName  The name of the database table to query, excluding the prefix '#__'.
	 *
	 * @return int Returns the resolved ID as an integer. If no matching ID is found, returns 0.
	 * @since   1.7.2
	 */
	private function getId(string $segment, array $query, string $tableName): int
	{
		$dbquery = $this->db->createQuery();

		$dbquery->select($this->db->quoteName('id'))
			->from($this->db->quoteName('#__' . $tableName))
			->where($this->db->quoteName('alias') . ' = :segment')
			->bind(':segment', $segment);

		$this->db->setQuery($dbquery);

		$id = (int) $this->db->loadResult();

		// Do we have a URL with ID?
		if ($id)
		{
			return $id;
		}

		$container = Factory::getContainer();
		$container->get('SiteRouter')->setTainted();


		$id = (int) $segment;

		if ($id)
		{
			$dbquery = $this->db->createQuery();
			$dbquery->select($this->db->quoteName('alias'))
				->from($this->db->quoteName('#__' . $tableName))
				->where($this->db->quoteName('id') . ' = :id')
				->bind(':id', $id, ParameterType::INTEGER);
			$this->db->setQuery($dbquery);
			$alias = $this->db->loadResult();

			if ($alias && $id . '-' . $alias != $segment)
			{
				$container = Factory::getContainer();
				$container->get('SiteRouter')->setTainted();
			}
		}

		return $id;
	}

	/**
	 * Retrieves the segment associated with the given event ID and query.
	 *
	 * This method delegates the retrieval of the segment based on an event ID
	 * and a query context, ensuring consistency with the underlying logic.
	 *
	 * @param   mixed  $id     The ID of the event for which the segment is being retrieved.
	 * @param   mixed  $query  The query context used to refine or assist the segment retrieval.
	 *
	 * @return mixed Returns the segment associated with the given event ID and query.
	 * @since   1.7.2
	 */
	public function getEventSegment($id, $query)
	{
		return $this->getSegment($id, $query);
	}

	/**
	 * Retrieves the event ID associated with the given segment or query.
	 *
	 * This method resolves and returns an event ID based on the provided segment
	 * and query by utilizing the 'sdajem_events' table.
	 *
	 * @param   mixed  $segment  The segment parameter, which could be an alias or an ID-like value.
	 * @param   mixed  $query    Additional query information, passed for context or possible use.
	 *
	 * @return int Returns the resolved event ID as an integer. If no matching ID is found, returns 0.
	 * @since   1.7.2
	 */
	public function getEventId($segment, $query)
	{
		return $this->getId($segment, $query, 'sdajem_events');
	}

	/**
	 * Retrieves the location ID based on the provided segment and query.
	 *
	 * @param   mixed  $segment  The segment to identify the location.
	 * @param   mixed  $query    The query criteria used to determine the location ID.
	 *
	 * @return mixed The location ID retrieved based on the inputs.
	 * @since 1.7.2
	 */
	public function getLocationId($segment, $query)
	{
		return $this->getId($segment, $query, 'sdajem_locations');
	}

	/**
	 * Retrieves the location segment based on the provided ID and query.
	 *
	 * @param   mixed  $id     The ID to identify the location segment.
	 * @param   mixed  $query  The query criteria used to determine the location segment.
	 *
	 * @return mixed The location segment retrieved based on the inputs.
	 * @since 1.7.2
	 */
	public function getLocationSegment($id, $query)
	{
		return $this->getSegment($id, $query);

	}

	/**
	 * @param   mixed  $segment
	 * @param   mixed  $query
	 *
	 * @return int
	 * @since 1.7.2
	 */
	public function getEventformId($segment, $query)
	{
		return $this->getId($segment, $query, 'sdajem_events');
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 * @return array
	 * @since 1.7.2
	 */
	public function getEventformSegment($id, $query)
	{
		return $this->getSegment($id, $query);
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return int
	 * @since 1.7.2
	 */
	public function getLocationformId($segment, $query): int
	{
		return $this->getId($segment, $query, 'sdajem_locations');
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return array
	 * @since 1.7.2
	 */
	public function getLocationformSegment($id, $query): array
	{
		return $this->getSegment($id, $query);
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return int
	 * @since 1.7.2
	 */
	public function getFittingId($segment, $query): int
	{
		return $this->getId($segment, $query, 'sdajem_fittings');
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return array
	 * @since 1.7.2
	 */
	public function getFittingSegment($id, $query): array
	{
		return $this->getSegment($id, $query);
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return int
	 * @since 1.7.2
	 */
	public function getFittingformId($segment, $query): int
	{
		return $this->getId($segment, $query, 'sdajem_fittings');
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return array
	 * @since 1.7.2
	 */
	public function getFittingformSegment($id, $query): array
	{
		return $this->getSegment($id, $query);
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return integer
	 * @since 1.7.2
	 */
	public function getAttendingId($segment, $query): int
	{
		return $this->getId($segment, $query, 'sdajem_attendings');
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return array
	 * @since 1.7.2
	 */
	public function getAttendingSegment($id, $query): array
	{
		return $this->getSegment($id, $query);
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return integer
	 * @since 1.7.2
	 */
	public function getAttendingformId($segment, $query): int
	{
		return $this->getId($segment, $query, 'sdajem_attendings');
	}

	/**
	 * @param   mixed  $id
	 * @param   mixed  $query
	 *
	 * @return array
	 * @since 1.7.2
	 */
	public function getAttendingformSegment($id, $query): array
	{
		return $this->getSegment($id, $query);
	}
}
