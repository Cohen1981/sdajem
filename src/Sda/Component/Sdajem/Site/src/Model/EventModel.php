<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Model;

use Joomla\CMS\Factory;
use Sda\Component\Sdajem\Administrator\Library\Item\Event;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Event model for the Joomla Events component.
 *
 * @since  1.0.0
 */
class EventModel extends \Sda\Component\Sdajem\Administrator\Model\EventModel
{
	/**
	 * Retrieves the details of a specific event item based on its primary key (id).
	 *
	 * @param   int|null  $pk  The primary key (id) of the event to retrieve. If not provided, it will be retrieved from the application input.
	 *
	 * @return Event The event item containing all relevant data, including its metadata, location, organizer, and statistics
	 *                       such as attendee counts and feedback.
	 * @since   1.5.3
	 */
	public function getItem($pk = null): Event
	{
		$app = Factory::getApplication();
		$pk  = (int) ($pk) ?: $app->input->getInt('id');

		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		$query = Event::getBaseQuery($query, $db);
		$query->where($db->quoteName('a.id') . ' = :eventId');

		$query->bind(':eventId', $pk);

		$db->setQuery($query);
		$data = $db->loadObject();

		return Event::createFromObject($data);
	}
}
