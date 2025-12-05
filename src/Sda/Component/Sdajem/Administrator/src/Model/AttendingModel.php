<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Language\Text;
use Sda\Component\Sdajem\Administrator\Library\Collection\AttendingTableItemsCollection;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemModelInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\Attending;
use Sda\Component\Sdajem\Administrator\Library\Item\AttendingTableItem;
use Sda\Component\Sdajem\Administrator\Table\AttendingTable;
use function defined;

defined('_JEXEC') or die;

/**
 * @package     Sda\Component\Sdajem\Administrator\Model
 * @since       1.0.0
 */
class AttendingModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_sdajem.attending';

	/**
	 * @param   array  $data     The data to populate the form with.
	 * @param   bool   $loadData If true, the form is loaded with data from the database. Default is true.
	 *
	 * @return Form|false
	 * @since 1.0.0
	 *
	 */
	public function getForm($data = array(), $loadData = true): Form|false
	{
		// Get the form.
		try
		{
			$form = $this->loadForm($this->typeAlias, 'attending', ['control' => 'jform', 'load_data' => $loadData]);
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @since   1.0.0
	 * @return  mixed  The data for the form.
	 * @throws Exception
	 */
	protected function loadFormData(): AttendingTableItem
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_sdajem.edit.attending.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->typeAlias, $data);

		return AttendingTableItem::createFromObject($data);
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since   1.0.0
	 *
	 * @param   AttendingTable  $table  The Table object
	 *
	 * @return  void
	 */
	protected function prepareTable($table): void
	{
		$table->generateAlias();
	}

	/**
	 * @param   int|null  $pk The primary key.
	 *
	 * @return AttendingTableItem
	 *
	 * @since 1.5.3
	 */
	public function getItem($pk = null): AttendingTableItem
	{
		return AttendingTableItem::createFromObject(parent::getItem($pk));
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
	 * Saves the given data, updating related event SVG data if specific conditions are met.
	 *
	 * @param   array  $data  The data to be saved, including details about the user and event.
	 *
	 * @return  bool  True on success, false on failure.
	 *
	 * @throws  Exception
	 * @since   1.6.0
	 */
	public function save($data)
	{
		$data = AttendingTableItem::createFromArray($data);

		$task = Factory::getApplication()->input->getCmd('task');

		if ($data->status == IntAttStatusEnum::NEGATIVE->value || $data->status == IntAttStatusEnum::GUEST->value || $task == 'deleteFitting')
		{
			$eventModel = new EventModel;
			$event      = $eventModel->getItem($data->event_id);

			$userFittings = (new FittingsModel)->getFittingsForUser($data->users_user_id);

			if ($event->svg)
			{
				$svg = (array) json_decode($event->svg);

				foreach ($svg as $key => $value)
				{
					foreach ($userFittings as $fitting)
					{
						if (str_contains($value, 'img_' . $fitting->id))
						{
							unset($svg[$key]);
						}
					}
				}

				$event->svg = (count($svg) > 0) ? json_encode($svg) : null;

				if (!$eventModel->save($event->toArray()))
				{
					Factory::getApplication()->enqueueMessage(Text::_('COM_SDAJEM_EVENT_SAVE_ERROR'), 'error');
				}
			}
		}
		elseif ($data->status == IntAttStatusEnum::POSITIVE->value && (empty($data->fittings) || $data->fittings == '[]'))
		{
			$userFittings   = (new FittingsModel)->getStandardFittingIdsForUser($data->users_user_id);
			$data->fittings = json_encode($userFittings);
		}

		if ($data->users_user_id)
		{
			return parent::save($data->toArray());
		}
		elseif ($data->fittings)
		{
			return parent::save($data->toArray());
		}
		else
		{
			$pks = (array) $data->id;

			return $this->delete($pks);
		}

	}
}
