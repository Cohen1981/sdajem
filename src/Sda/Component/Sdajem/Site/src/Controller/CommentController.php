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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Sda\Component\Sdajem\Administrator\Library\Item\Comment;
use Sda\Component\Sdajem\Administrator\Library\Item\CommentTableItem;

/**
 * CommentController class to manage comment-related operations.
 * @since 1.5.0
 */
class CommentController extends FormController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $view_item = 'commentform';

	/**
	 * @var string
	 * @since 1.5.0
	 */
	protected $view_list = 'comments';

	/**
	 * Method to add a new record.
	 * @since 1.5.0
	 */
	public function addCommentToEvent()
	{
		parent::add();

		$id = $this->input->get('eventId', null, 'int');
		$this->app->setUserState('com_sdajem.comment.sdajem_event_id', $id);
		$this->app->setUserState('com_sdajem.event.callContext', $this->input->get('callContext', ''));
	}

	public function save($key = null, $urlVar = null)
	{
		$data = $this->input->post->get('jform', [], 'array');

		$data['sdajem_event_id'] = ($data['sdajem_event_id'] <> '') ? $data['sdajem_event_id'] : $this->app->getUserState('com_sdajem.comment.sdajem_event_id');
		$data['users_user_id'] = ($data['users_user_id'] <> '') ? $data['users_user_id'] : Factory::getApplication()->getIdentity()->id;
		$this->input->post->set('jform', $data);

		return parent::save($key, $urlVar);
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 * @param   string  $name    The model name. Optional.
	 *
	 * @return  BaseDatabaseModel  The model.
	 */
	public function getModel($name = 'commentform', $prefix = '', $config = ['ignore_request' => true])
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
	 * @throws \Exception
	 */
	protected function allowAdd($data = [])
	{
		$user = Factory::getApplication()->getIdentity();

		return $user->authorise('core.create', 'com_sdajem');
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @since   __DEPLOY_VERSION__
	 *
	 * @param   string  $key  The name of the key for the primary key; default is id.
	 * @param   array   $data  An array of input data.
	 *
	 * @return  boolean
	 * @throws \Exception
	 */
	protected function allowEdit($data = [], $key = 'id')
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

		if ($user->authorise('core.edit.own', 'com_sdajem'))
		{
			return ($record->created_by == $user->id);
		}

		return false;
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
	public function cancel($key = null)
	{
		return parent::cancel($key);
	}

	public function delete(): bool
	{
		$pks = $this->input->get('cid') ?? $this->input->get('id');
		$this->app->setUserState('com_sdajem.event.callContext', $this->input->get('callContext', ''));

		if (!is_array($pks))
		{
			$pks = [$pks];
		}

		$return = $this->getModel()->delete($pks);

		$this->setRedirect(Route::_($this->getReturnPage(), false));

		return $return;
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

	protected function postSaveHook(BaseDatabaseModel $model, $validData = [])
	{
		// ToDo: Vermutlich werden wir nicht immer aus einem Modal Kontext kommen
		$id = $model->state->get('commentform.id');
		$model->state->set('commentform.id', $id);
		$this->input->set('layout', 'modalreturn');
		$return = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend(
				$id
			);

		$this->setRedirect(Route::_($return));
	}

}
