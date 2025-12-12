<?php

/**
 * @package     Sda\Component\Sdajem\Site\Controller
 * @subpackage
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Sda\Component\Sdajem\Site\Controller;

defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Component\Categories\Administrator\Model\CategoryModel;
use Joomla\Registry\Registry;
use Sda\Component\Sdajem\Administrator\Library\Enums\EventStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Enums\IntAttStatusEnum;
use Sda\Component\Sdajem\Administrator\Library\Item\AttendingTableItem;
use Sda\Component\Sdajem\Administrator\Library\Item\EventTableItem;
use Sda\Component\Sdajem\Administrator\Model\AttendingsModel;
use Sda\Component\Sdajem\Site\Model\AttendingformModel;
use Sda\Component\Sdajem\Site\Model\AttendingModel;
use Sda\Component\Sdajem\Administrator\Model\AttendingModel as AttendingModelAdmin;
use Sda\Component\Sdajem\Site\Model\EventformModel;
use Sda\Component\Sdajem\Site\Model\EventModel;

/**
 * The URL view item variable.
 *
 * @var    string
 * @since  __DEPLOY_VERSION__
 */
class EventController extends FormController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $view_item = 'eventform';

	/**
	 * @var string
	 * @since 1.0.9
	 */
	protected $view_list = 'events';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  BaseDatabaseModel  The model.
	 * @since   __DEPLOY_VERSION__
	 *
	 */
	public function getModel($name = 'eventform', $prefix = '', $config = ['ignore_request' => true]): BaseDatabaseModel
	{
		return parent::getModel($name, $prefix, ['ignore_request' => false]);
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 * @throws Exception
	 */
	protected function allowAdd($data = []): bool
	{
		$user = Factory::getApplication()->getIdentity();

		return $user->authorise('core.create', 'com_sdajem');
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 * @throws Exception
	 * @since   __DEPLOY_VERSION__
	 *
	 */
	protected function allowEdit($data = [], $key = 'id'): bool
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;

		if (!$recordId)
		{
			return false;
		}

		// Need to do a lookup from the model.
		$record = $this->getModel()->getItem($recordId);

		$user = Factory::getApplication()->getIdentity();

		if ($user->authorise('core.edit', 'com_sdajem'))
		{
			return true;
		}

		// Fallback on edit.own.
		if ($user->authorise('core.edit.own', 'com_sdajem'))
		{
			return ($record->created_by == $user->id);
		}

		return false;
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to
	 *                           avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 * @since   __DEPLOY_VERSION__
	 */
	public function save($key = null, $urlVar = null): bool
	{
		$data = $this->app->input->post->get('jform', array(), 'array');

		if ($data['sdajem_location_id'] == 0 || $data['sdajem_location_id'] == '')
		{
			$data['sdajem_location_id'] = null;
		}

		$this->app->input->post->set('jform', $data);

		return parent::save($key, $urlVar = null);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancel($key = null): bool
	{
		$result = parent::cancel($key);
		$this->setRedirect(Route::_($this->getReturnPage(), false));

		return $result;
	}

	/**
	 * @since 1.0.1
	 * @return bool
	 */
	public function delete(): bool
	{
		$pks = $this->input->get('cid') ?? $this->input->get('id');

		if (!is_array($pks))
		{
			$pks = [$pks];
		}

		$eventFormModel = new EventformModel;

		$this->setRedirect(Route::_($this->getReturnPage(), false));

		return $eventFormModel->delete($pks);
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
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id'): string
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

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . (int) $recordId;
		}

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
	protected function getReturnPage(): string
	{
		$return = $this->input->get('return', null, 'base64');

		if (empty($return) || !Uri::isInternal(base64_decode($return)))
		{
			return Uri::base();
		}

		return base64_decode($return);
	}

	/**
	 * @since 1.2.4
	 */
	private function setViewLevel(): void
	{
		$params = ComponentHelper::getParams('com_sdajem');
		$this->setAccessLevel($params->get('sda_public_planing'));
	}

	/**
	 * Set the Event Status to open
	 * @since 1.0.9
	 */
	public function open(): void
	{
		$this->setEventStatus(EventStatusEnum::OPEN);
		$this->setViewLevel();
		$this->setRedirect(Route::_($this->getReturnPage(), false));
	}

	/**
	 * Set the event status to applied
	 * @since 1.2.0
	 */
	public function applied(): void
	{
		$this->setEventStatus(EventStatusEnum::APPLIED);
		$this->setViewLevel();
		$this->setRedirect(Route::_($this->getReturnPage(), false));
	}

	/**
	 * Set the event status to canceled
	 * @since 1.2.0
	 */
	public function canceled(): void
	{
		$this->setEventStatus(EventStatusEnum::CANCELED);
		$this->setViewLevel();
		$this->setRedirect(Route::_($this->getReturnPage(), false));
	}

	/**
	 * Set the event status to confirmed
	 * @since 1.2.0
	 */
	public function confirmed(): void
	{
		$this->setEventStatus(EventStatusEnum::CONFIRMED);
		$this->setAccessLevel(1);
		$this->setRedirect(Route::_($this->getReturnPage(), false));
	}

	/**
	 * @since 1.0.9
	 */
	public function planing(): void
	{
		$this->setEventStatus(EventStatusEnum::PLANING);
		$this->setViewLevel();
		$this->setRedirect(Route::_($this->getReturnPage(), false));
	}

	/**
	 * @param   EventStatusEnum  $enum
	 *
	 * @return void
	 * @since 1.2.0
	 */
	protected function setEventStatus(EventStatusEnum $enum): void
	{
		$eventId = $this->input->get('eventId');

		if ($eventId != null)
		{
			/* @var EventformModel $event */
			$event = $this->getModel();
			$event->updateEventStatus($eventId, $enum);
		}
	}

	protected function setAccessLevel(int $access): void
	{
		$eventId = $this->input->get('eventId');

		if ($eventId != null)
		{
			/* @var EventformModel $event */
			$event = $this->getModel();
			$event->updateEventAccess($eventId, $access);
		}
	}

	/**
	 * Saves a working state of the planingTool
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function savePlan(): void
	{
		$svg = $_POST['svg'];
		if (empty($svg))
		{
			return;
		}
		$this->app->setUserState('com_sdajem.callContext', $this->input->get('callContext', ''));

		/** @var EventModel $event */
		$event = $this->getModel('Event');
		$data  = $event->getItem($_POST['id']);

		$reg = new Registry($_POST['svg']);

		$data->svg = json_encode($reg);

		$eventForm = new EventformModel();
		try
		{
			$eventForm->save($data->toArray());
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Deletes a fitting from the event
	 * @return void
	 * @since 1.2.0
	 */
	public function saveAttendings(): void
	{
		$attendings = $_POST['attendings'];
		$eventId    = $_POST['eventId'];
	}
	public function deleteFitting()
	{
		$input     = $this->input;
		$fittingId = $input->getInt('id');
		$eventId   = $input->getInt('eventId');

		$attendings    = (new AttendingsModel)->getAttendingsToEvent($eventId);
		$attendingForm = new AttendingFormModel();

		foreach ($attendings as $attending)
		{
			if (isset($attending->fittings))
			{
				$attFittings = (array) json_decode($attending->fittings, false);

				foreach ($attFittings as $key => $value)
				{
					if ($value == $fittingId)
					{
						unset($attFittings[$key]);
					}
				}

				$attending->fittings = (count($attFittings) > 0) ? json_encode($attFittings) : null;

				if (!$attendingForm->save($attending->toArray()))
				{
					Factory::getApplication()->enqueueMessage(Text::_('COM_SDAJEM_ATTENDING_SAVE_ERROR'), 'error');
				}
			}
		}
	}

	/**
	 * @return void
	 * @throws Exception
	 * @since 1.5.0
	 */
	public function changeTpl(): void
	{
		$app      = Factory::getApplication();
		$currTmpl = $this->input->get('currTmpl', 'default');

		if ($currTmpl == 'cards')
		{
			$template = 'default';
		}
		else
		{
			$template = 'cards';
		}

		$app->setUserState('com_sdajem.events.event_tpl', $template);

		$user = $app->getIdentity();

		if (!$user->guest)
		{
			$user->setParam('events_tpl', $template);
			$user->save();
		}

		$this->setRedirect(Route::_($this->getReturnPage(), false));
	}

	/**
	 * @since 1.5.0
	 *
	 * @param                      $validData
	 * @param   BaseDatabaseModel  $model
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function postSaveHook(BaseDatabaseModel $model, $validData = []): void
	{
		$componentParams = ComponentHelper::getParams('com_sdajem');

		if ($model->state->get('eventform.new') && $componentParams->get('sda_mail_on_new_event'))
		{
			$mailer = Factory::getContainer()->get(MailerFactoryInterface::class)->createMailer();

			// Define necessary variables
			$subject = Text::_('NEW_EVENT_SAVED') . ': '
				. $validData['title'] . ' '
				. HTMLHelper::date($validData['startDateTime'], 'd.m.Y')
				. ' - ' . HTMLHelper::date($validData['endDateTime'], 'd.m.Y');
			$body    = Text::_('COM_SDAJEM_FIELD_REGISTERUNTIL_LABEL') . ': '
				. HTMLHelper::date($validData['registerUntil'],	'd.m.Y'
				);

			$recipientsUsers = Access::getUsersByGroup($componentParams->get('sda_usergroup_mail'));
			$userFactory     = Factory::getContainer()->get(UserFactoryInterface::class);

			foreach ($recipientsUsers as $recipientUser)
			{
				$mailer->addRecipient($userFactory->loadUserById($recipientUser)->email);
			}

			// Set subject, and body of the email
			$mailer
				->isHTML(true)
				->setSubject($subject)
				->setBody($body);

			// Set plain text alternative body (for email clients that don't support HTML)
			$mailer->AltBody = strip_tags($body);

			// Send the email and check for success or failure
			try
			{
				$send = $mailer->Send(); // Attempt to send the email

				if ($send !== true)
				{
					echo 'Error: ' . $send->__toString(); // Display error message if sending fails
				}
				else
				{
					Factory::getApplication()->enqueueMessage(Text::_('SDA_EMAIL_EVENT_SUCCESS'), 'info');
				}
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Saves the box configuration for a specific event form, updating event parameters.
	 *
	 * @return void
	 * @throws Exception
	 * @since 1.6.2
	 */
	public function saveBoxForm()
	{
		$input = $this->input;

		// Save the call context to the user state for later use
		$this->app->setUserState('com_sdajem.callContext', $input->get('callContext', ''));

		// If the event id is set, we're editing an existing event, otherwise we're ignoring the request
		if ($input->get('eventId') != null)
		{
			$eventFormModel = new EventformModel();

			$eventData = $eventFormModel->getItem($input->get('eventId'));

			// Get the component parameters for later use as defaults
			$compParams = ComponentHelper::getParams('com_sdajem');

			// Parse the event parameters into a registry object
			$params = new Registry($eventData->params);

			// Update the box dimensions
			$params->set('sda_planing_x', $input->get('boxX', $compParams->get('sda_planing_x')));
			$params->set('sda_planing_y', $input->get('boxY', $compParams->get('sda_planing_y')));
			$eventData->params = $params->toString();

			// Reset the SVG data to an empty string. If we don't do this, there will be a problem when gear is outside the view box
			$eventData->svg = '';

			// Save the event data
			$eventFormModel->save($eventData->toArray());

			$this->setRedirect(Route::_($this->getReturnPage(), false));
		}
	}
}
