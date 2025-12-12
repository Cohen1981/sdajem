<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Utilities\ArrayHelper;
use Sda\Component\Sdajem\Administrator\Library\Item\EventTableItem;
use Sda\Component\Sdajem\Administrator\Table\EventTable;
use stdClass;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Administrator\Model
 * @since       1.0.0
 */
class EventModel extends AdminModel
{
	/**
	 * @var    string
	 * The type alias for this content type.
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_sdajem.event';

	/**
	 * @since 1.0.0
	 *
	 * @param   boolean  $loadData  If true, the form is loaded with data from the database. Default is true.
	 * @param   array    $data      The data to populate the form with. Default is an empty array.
	 *
	 * @return Form|false
	 * @throws Exception
	 */
	public function getForm($data = array(), $loadData = true): Form|false
	{
		// Get the form.
		try
		{
			$form = $this->loadForm($this->typeAlias, 'event', ['control' => 'jform', 'load_data' => $loadData]);
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
	 * Method to get a single record.
	 * @param   int|null       $pk The id of the primary key.
	 * @return  EventTableItem
	 * @since 1.0.0
	 */
	public function getItem($pk = null): EventTableItem
	{
		return EventTableItem::createFromObject(parent::getItem($pk));
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @since   1.0.0
	 * @return  EventTableItem  The data for the form.
	 * @throws Exception
	 */
	protected function loadFormData(): EventTableItem
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_sdajem.edit.event.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->typeAlias, $data);

		if (is_array($data))
		{
			$data = ArrayHelper::toObject($data, stdClass::class);
		}

		return EventTableItem::createFromObject($data);
	}


	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since   1.0.0
	 *
	 * @param   EventTable  $table  The Table object
	 *
	 * @return  void
	 * @throws Exception
	 */
	protected function prepareTable($table): void
	{
		$table->check();
		$table->generateAlias();
	}

	/**
	 * Method to delete one or more records.
	 *
	 * @param   array|int[]  &$pks  Array of primary key IDs to delete.
	 *
	 * @return  bool  True on success, false on failure.
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function delete(&$pks): bool
	{
		$pks = ArrayHelper::toInteger((array) $pks);

		$attendingModel  = new AttendingModel;
		$attendingsModel = new AttendingsModel;

		$commentsModel = new CommentsModel;
		$commentModel  = new CommentModel;

		foreach ($pks as $pk)
		{
			$attendings = $attendingsModel->getAttendingIdsToEvent($pk);
			$result[]   = $attendingModel->delete($attendings);

			$comments = $commentsModel->getCommentIdsToEvent($pk);
			$result[] = $commentModel->delete($comments);
		}

		if (\in_array(false, $result, true))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_SDAJEM_ERROR_DELETE_EVENT'), 'error');

			return false;
		}

		return parent::delete($pks);
	}

	/**
	 * Saves a given data record.
	 *
	 * @param   mixed  $data  The data to be saved.
	 *
	 * @return  bool    True on success, false on failure.
	 * @since 1.7.2
	 */
	public function save($data): bool
	{
		$return = parent::save($data);

		if ($return)
		{
			$id = $this->state->get('eventform.id');
			$this->makeIcal($this->getItem($id));
		}

		return $return;
	}

	/**
	 * Generates an iCalendar (.ics) file for a given event and writes it to the filesystem.
	 *
	 * @param   EventTableItem  $event  The event object containing the data required to generate the iCalendar file.
	 *
	 * @return void
	 * @since 1.7.2
	 *
	 */
	private function makeIcal(EventTableItem $event): void
	{
		$kb_ical = fopen(JPATH_SITE . '/files/' . $event->alias . '.ics', 'w') or die('Datei kann nicht gespeichert werden!');

		$kb_current_time = HTMLHelper::date('now', 'Ymd\THi\Z') . '00';

		$locationString = '';

		if ($event->sdajem_location_id)
		{
			$location = (new LocationModel)->getItem($event->sdajem_location_id);

			if (!empty($location->latlng))
			{
				$locationString = urlencode($location->latlng);
			}
			else
			{
				$locationString = $locationString . (!empty($location->street)) ? $location->street : '';
				$locationString .= (!empty($location->postalCode)) ? ', ' . $location->postalCode : '';
				$locationString .= (!empty($location->city)) ? ' ' . $location->city : '';
			}
		}

		$eol = "\r\n";

		$kb_ics_content =
			'BEGIN:VCALENDAR' . $eol .
			'VERSION:2.0' . $eol .
			'PRODID:https://www.survivants-d-acre.de' . $eol .
			'METHOD:REQUEST' . $eol .
			'CALSCALE:GREGORIAN' . $eol .
			'BEGIN:VEVENT' . $eol .
			'DTSTART:' . $event->getStart(true) . $eol .
			'DTEND:' . $event->getEnd(true) . $eol .
			'LOCATION:' . $locationString . $eol .
			'DTSTAMP:' . $kb_current_time . $eol .
			'SUMMARY:' . $event->title . $eol .
			'URL;VALUE=URI:' . $event->url . $eol .
			'DESCRIPTION:' . $event->description . $eol .
			'UID:' . $event->id . '-' . $event->getStart(true) . '-'
			. $event->getEnd(true) . $eol .
			'END:VEVENT' . $eol .
			'END:VCALENDAR';

		fwrite($kb_ical, $kb_ics_content);

		fclose($kb_ical);
	}
}
