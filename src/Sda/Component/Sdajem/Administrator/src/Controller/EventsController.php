<?php
/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Sda\Component\Sdajem\Administrator\Model\EventModel;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * EventsController class handles administrative functionalities related to the Events component.
 *
 * @since 1.0.0
 */
class EventsController extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @since   1.0
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Array of configuration parameters.
	 *
	 * @return  BaseDatabaseModel
	 */
	public function getModel($name = 'Event', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * @since 1.0.8
	 * @return bool
	 */
	public function delete()
	{
		$pks = $this->input->get('cid');

		/*
		$attendingFormModel = new AttendingModel();
		$attendingsModel    = new AttendingsModel();

		foreach ($pks as &$pk)
		{
			$attendings = $attendingsModel->getAttendingsIdToEvent($pk);
			$attResult  = $attendingFormModel->delete($attendings);
		}

		$eventFormModel = new EventModel();

		if ($attResult)
		{*/
		$eventFormModel = new EventModel;
		$result         = $eventFormModel->delete($pks);
		//}

		$this->setRedirect(
			Route::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(),
				false
			)
		);

		return $result;
	}
}
