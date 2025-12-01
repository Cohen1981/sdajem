<?php
/**
 * @package     Sda\Component\Sdajem\Site\Controller
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Controller;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Sda\Component\Sdajem\Site\Model\LocationModel;

/**
 * @since   __DEPLOY_VERSION__
 */
class LocationController extends FormController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $view_item = 'locationform';

	/**
	 * @var string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $view_list = 'locations';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  BaseDatabaseModel  The model.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getModel($name = 'locationform', $prefix = '', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, ['ignore_request' => false]);
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @throws \Exception
	 * @since   __DEPLOY_VERSION__
	 */
	protected function allowAdd($data = [])
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
	 *
	 * @throws \Exception
	 * @since   __DEPLOY_VERSION__
	 */
	protected function allowEdit($data = [], $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;

		if (!$recordId)
		{
			return false;
		}

		// Need to do a lookup from the model.
		$record     = $this->getModel()->getItem($recordId);

		$user = Factory::getApplication()->getIdentity();

		if ($user->authorise('core.edit', 'com_sdajem'))
		{
			return true;
		}

		if ($user->authorise('core.edit.own', 'com_sdajem'))
		{
			return ($record->created_by == $user->id);
		}

		return false;
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function cancel($key = null)
	{
		return parent::cancel($key);
	}

	/**
	 * Delete records based on provided IDs.
	 *
	 * Retrieves the selected IDs from the request, checks if each
	 * location is in use, and deletes those that are not. Displays
	 * warnings for locations that cannot be deleted due to usage.
	 * Redirects the user to the return page after the process.
	 *
	 * @return  boolean  True on successful execution.
	 * @since   1.5.3
	 */
	public function delete(): bool
	{
		$pks = ArrayHelper::toInteger($this->input->get('cid'));

		foreach ($pks as &$pk)
		{
			/** @var LocationModel $model */
			$model = $this->getModel('location');

			if ($model->countUsage($pk) > 0)
			{
				$item = $model->getItem($pk);
				$this->app->enqueueMessage($item->title . ' ' . Text::_('COM_SDAEJEM_LOCATION_IN_EVENTS'), 'warning');
			}
			else
			{
				$this->getModel()->delete($pk);
			}
		}

		$this->setRedirect(Route::_($this->getReturnPage(), false));

		return true;
	}

	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return  string    The return URL.
	 *
	 * @since   __DEPLOY_VERSION__
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

	/**
	 * Perform additional operations after saving the form data.
	 *
	 * This method handles different redirection scenarios based on the task or layout
	 * provided, such as redirecting to a modal return layout, a new form, or the default list view.
	 *
	 * @param   BaseDatabaseModel  $model      The model used to save the data.
	 * @param   array              $validData  The validated data being saved.
	 *
	 * @return  void
	 * @since   1.5.3
	 */
	protected function postSaveHook(BaseDatabaseModel $model, $validData = [])
	{
		if ($this->input->get('layout') === 'modal' && $this->task === 'save')
		{
			$id = $model->state->get('locationform.id');
			$model->state->set('locationform.id', $id);
			$this->input->set('layout', 'modalreturn');
			$return = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($id);
		}
		elseif ($this->input->get('task') === 'save2new')
		{
			$return = 'index.php?option=' . $this->option . '&view=locations&task=location.add';
		}
		else
		{
			$return = 'index.php?option=' . $this->option . '&view=locations';
		}

		$this->setRedirect(Route::_($return, false));
	}
}
