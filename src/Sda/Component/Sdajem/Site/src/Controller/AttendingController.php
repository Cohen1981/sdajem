<?php
/**
 * @package     Sda\Component\Sdajem\Site\Controller
 * @subpackage     com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 * @since          1.5.3
 */

namespace Sda\Component\Sdajem\Site\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\AttendingTableItem;
use Sda\Component\Sdajem\Administrator\Library\Item\Event;
use Sda\Component\Sdajem\Administrator\Model\FittingsModel;
use Sda\Component\Sdajem\Site\Model\AttendingModel;
use Sda\Component\Sdajem\Site\Model\EventModel;

/**
 * @package     Sda\Component\Sdajem\Site\Controller
 * @since       1.0.0
 */
class AttendingController extends FormController
{

	/**
	 * The URL view item variable.
	 * @var string
	 * @since 1.0.1
	 */
	protected $view_item = 'attendingform';

	/**
	 * @var string
	 * @since 1.0.1
	 */
	protected $view_list = 'attendings';

	public function getModel($name = 'attendingform', $prefix = '', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, ['ignore_request' => false]);
	}

	/**
	 * @return array
	 */
	private function getPks()
	{
		$pks = [];

		if ($this->input->get('event_id'))
		{
			$pks[0] = $this->input->get('event_id');
		}
		else
		{
			$pks = $this->input->get('cid');
		}

		return $pks;
	}

	/**
	 * @since 1.0.1
	 *
	 * @param   null  $urlVar
	 * @param   null  $key
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function save($key = null, $urlVar = null)
	{
		// get the input data
		$input = $this->input->get('jform');

		// if the user id is not set, we set it to the current logged-in user id
		if (!$input['users_user_id'])
		{
			$input['users_user_id'] = Factory::getApplication()->getIdentity()->id;
		}

		if (empty($input('id')))
		{
			$data = AttendingModel::getAttendingToEvent($input['users_user_id'], $input['event_id']);
		}
		else
		{
			$data = (new AttendingModel())->getItem($input['id']);
		}

		// attending exist so we set the id for updating the record
		if (!empty($data->id))
		{
			$this->input->set('id', $data->id);
		}

		// if the event is planing, we set the event status to planing. Otherwise, we set it to open. We use this to determine if we have an interest or a real attending.
		$event                 = (new EventModel())->getItem($input['event_id']);
		$input['event_status'] = ($event->eventStatusEnum == EventStatusEnum::PLANING) ? EventStatusEnum::PLANING->value : EventStatusEnum::OPEN->value;

		// if the user is attending, we need to get the standard fittings
		if ($input['status'] == IntAttStatusEnum::POSITIVE->value)
		{
			$fittings = (new FittingsModel())->getFittingsForUser($data->users_user_id);
			$ids      = [];

			if ($fittings)
			{
				foreach ($fittings as $fitting)
				{
					if ($fitting->standard)
					{
						$ids[] = $fitting->id;
					}
				}

				$input['fittings'] = json_encode($ids);
			}
		}

		$this->input->post->set('jform', $input);

		$this->setRedirect(Route::_($this->getReturnPage(), false));

		return parent::save($key, $urlVar);
	}

	/**
	 * @param   null              $eventId
	 * @param   null              $userId
	 * @param   IntAttStatusEnum  $attStatus
	 *
	 * @throws Exception
	 * @since 1.6.2
	 */
	private function setAttending(
		$eventId = null,
		$userId = null,
		IntAttStatusEnum $attStatus = IntAttStatusEnum::NA
	): void {
		$pks = $this->getPks();

		// if called from event page, we get the event_id from input
		if ($this->input->get('event_id'))
		{
			$pks[0] = $this->input->get('event_id');
		}
		// if called from another function we assume $ecentId is set
		elseif ($eventId !== null)
		{
			$pks[0] = $eventId;
		}
		// if called from list page, we get the event_id from the input
		else
		{
			$pks = $this->input->get('cid');
		}

		$this->app->setUserState('com_sdajem.callContext', $this->input->get('callContext', ''));

		if (count($pks) >= 0)
		{
			if ($userId !== null)
			{
				$currUser = $userId;
			}
			else
			{
				$currUser = Factory::getApplication()->getIdentity();
			}

			foreach ($pks as $id)
			{
				$attending = AttendingModel::getAttendingToEvent($currUser->id, $id);

				$event = Event::createFromObject((new EventModel())->getItem($id));

				$eventStatus = ($event->eventStatusEnum == EventStatusEnum::PLANING) ? EventStatusEnum::PLANING : EventStatusEnum::OPEN;

				$this->input->set('id', $attending->id);

				$data                = new AttendingTableItem();
				$data->id            = (int) $attending->id;
				$data->event_id      = (int) $id;
				$data->users_user_id = (int) $currUser->id;
				$data->status        = (int) $attStatus->value;
				$data->event_status  = (int) $eventStatus->value;

				$this->input->set('jform', $data->toArray());

				$this->save();
			}
		}
	}

	/**
	 * @since 1.0.1
	 *
	 * @param   null  $userId
	 * @param   null  $eventId
	 *
	 * @throws Exception
	 */
	public function attend($eventId = null, $userId = null): void
	{
		$this->setAttending($eventId, $userId, IntAttStatusEnum::POSITIVE);
	}

	/**
	 * @since 1.0.1
	 *
	 * @param   null  $userId
	 * @param   null  $eventId
	 *
	 * @throws Exception
	 */
	public function unattend($eventId = null, $userId = null):void
	{
		$this->setAttending($eventId, $userId, IntAttStatusEnum::NEGATIVE);
	}

	/**
	 * @param   null  $eventId
	 * @param   null  $userId
	 *
	 * @since 1.6.2
	 */
	public function guest($eventId = null, $userId = null): void
	{
		$this->setAttending($eventId, $userId, IntAttStatusEnum::GUEST);
	}

	/**
	 * Method to check if you can add a new record.
	 * Extended classes can override this if necessary.
	 *
	 * @since   1.6
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowAdd($data = [])
	{
		return !$this->app->getIdentity()->guest;
	}

	/**
	 * Method to check if you can edit an existing record.
	 * Extended classes can override this if necessary.
	 *
	 * @since   1.6
	 *
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 * @param   array   $data  An array of input data.
	 *
	 * @return  boolean
	 */
	protected function allowEdit($data = [], $key = 'id')
	{
		return !$this->app->getIdentity()->guest;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 * @param   integer  $recordId  The primary key id for the item.
	 *
	 * @return  string    The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = 0, $urlVar = 'id')
	{
		// Need to override the parent method completely.
		$tmpl   = $this->input->get('tmpl');
		$append = '';
		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}
		$append .= '&layout=edit';
		$append .= '&' . $urlVar . '=' . (int) $recordId;
		$itemId = $this->input->getInt('Itemid');
		$return = $this->getReturnPage();
		if ($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}
		if ($return)
		{
			$append .= '&return=' . base64_encode($return);
		}

		return $append;
	}

	/**
	 * Get the return URL.
	 * If a "return" variable has been passed in the request
	 *
	 * @since   __DEPLOY_VERSION__
	 * @return  string    The return URL.
	 */
	protected function getReturnPage()
	{
		$return = $this->input->get('return', null, 'base64');
		if (empty($return) || !Uri::isInternal(base64_decode($return)))
		{
			return Uri::base();
		}

		return base64_decode($return);
	}
}
