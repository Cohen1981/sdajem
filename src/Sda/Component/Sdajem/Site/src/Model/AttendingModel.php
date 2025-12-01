<?php
/**
 * @package     Sda\Component\Sdajem\Site\Model
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\QueryInterface;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemModelInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
use Sda\Component\Sdajem\Administrator\Library\Item\AttendingTableItem;
use function defined;

defined('_JEXEC') or die;

/**
 * Attending model for the Joomla Events component.
 *
 * @since  1.5.3
 *
 */
class AttendingModel extends BaseDatabaseModel implements ItemModelInterface
{
	/**
	 * @param   int|null  $pk  Pk for the event
	 *
	 * @return  Attending Object or null
	 *
	 * @throws Exception
	 * @since   1.0.0
	 */
	public function getItem(int $pk = null):Attending
	{
		$app = Factory::getApplication();
		$pk  = ($pk) ?: $app->input->getInt('id');

		try
		{
			$db    = $this->getDatabase();
			$query = $db->getQuery(true);

			$query = Attending::getBaseQuery($query, $db);

			$query->where($db->quoteName('a.id') . ' = :attendingId');

			$query->bind(':attendingID', $pk);

			$db->setQuery($query);
			$data = $db->loadObject();

			if (empty($data))
			{
				throw new Exception(Text::_('COM_SDAJEM_ERROR_ATTENDING_NOT_FOUND'), 404);
			}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$this->item = new Attending;
		}

		return Attending::createFromObject($data);
	}

	/**
	 * @param   int|null  $userId   The user id.
	 * @param   int       $eventId  The event id.
	 *
	 * @return AttendingTableItem
	 *
	 * @throws Exception
	 * @since 1.5.3
	 */
	public static function getAttendingToEvent(int $userId = null, int $eventId): Attending
	{
		if (!$userId)
		{
			$userId = Factory::getApplication()->getIdentity()->id;
		}

		try
		{
			$db    = Factory::getContainer()->get(DatabaseInterface::class);
			$query = $db->getQuery(true);

			$query = Attending::getBaseQuery($query, $db);

			$query->where($db->quoteName('a.users_user_id') . ' = :userId')
				->extendWhere('AND', $db->quoteName('a.event_id') . ' = :eventId');

			$query->bind(':userId', $userId)
				->bind(':eventId', $eventId);

			$db->setQuery($query);
			$data = $db->loadObject();

			if (empty($data))
			{
				$data = new \stdClass;
			}
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return Attending::createFromObject($data);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since   1.5.3
	 */
	protected function populateState():void
	{
		$app = Factory::getApplication();

		$this->setState('attending.id', $app->input->getInt('id'));
		$this->setState('params', $app->getParams());
	}
}
