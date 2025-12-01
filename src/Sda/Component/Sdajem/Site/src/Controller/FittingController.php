<?php
/**
 * @package     Sda\Component\Sdajem\Site\Controller
 * @subpackage  com_sdajem
 * @copyright   (C)) 2025 Survivants-d-Acre <https://www.survivants-d-acre.com>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       1.5.3
 */

namespace Sda\Component\Sdajem\Site\Controller;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ModelInterface;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Sda\Component\Sdajem\Administrator\Model\AttendingsModel;
use Sda\Component\Sdajem\Administrator\Model\AttendingModel;
use Sda\Component\Sdajem\Site\Model\FittingformModel;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @since 1.5.3
 */
class FittingController extends FormController
{
	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  __DEPLOY_VERSION__
	 */
	protected $view_item = 'fittingform';

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
	public function getModel($name = 'fittingform', $prefix = 'site', $config = ['ignore_request' => true]):ModelInterface
	{
		return parent::getModel($name, $prefix, ['ignore_request' => false]);
	}

	/**
	 * Method to cancel an edit.
	 * @param   integer|null  $key  The name of the primary key of the URL variable.
	 * @return  boolean  True if access level checks pass, false otherwise.
	 * @since   1.5.3
	 */
	public function cancel($key = null)
	{
		$result = parent::cancel($key);
		$this->setRedirect(Route::_($this->getReturnPage(), false));

		return $result;
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
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = [], $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = $this->app->getIdentity();

		if (!$recordId)
		{
			return parent::allowEdit($data, $key);
		}

		// Check edit on the record asset (explicit or inherited)
		if ($user->authorise('core.edit', 'com_sdajem'))
		{
			return true;
		}

		if ($user->authorise('core.edit.own', 'com_sdajem'))
		{
			// Existing record already has an owner, get it
			$record = $this->getModel()->getItem($recordId);

			if (empty($record))
			{
				return false;
			}

			// Grant if current user is owner of the record
			return $user->id == $record->user_id;
		}

		return false;
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   1.6
	 */
	public function edit($key = null, $urlVar = 'id')
	{
		$result = parent::edit($key, $urlVar);

		if (!$result) {
			$this->setRedirect(Route::_($this->getReturnPage(), false));
		}

		return $result;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string    The arguments to append to the redirect URL.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getRedirectToItemAppend($recordId = 0, $urlVar = 'id')
	{
		// Need to override the parent method completely.
		$tmpl = $this->input->get('tmpl');
		$append = '';
		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl=' . $tmpl;
		}
		$append .= '&layout=edit';
		$append .= '&' . $urlVar . '=' . (int) $recordId;
		$itemId = $this->input->getInt('Itemid');
		$return = $this->getReturnPage();
		if ($itemId) {
			$append .= '&Itemid=' . $itemId;
		}
		if ($return) {
			$append .= '&return=' . base64_encode($return);
		}
		return $append;
	}

	/**
	 * @param   BaseDatabaseModel  $model
	 * @param   array              $validData
	 *
	 * @throws Exception
	 * @since   1.1.5
	 */
	protected function postSaveHook(BaseDatabaseModel $model, $validData = [])
	{
		parent::postSaveHook($model, $validData);
		$newItem = $model->getState('fittingform.new', false);
		$itemId = $model->getState('fittingform.id', 0);

		if ($newItem)
		{
			if ($itemId != 0 && $validData['standard'] == 1)
			{
				$userId = ($validData['user_id']) ? ($validData['user_id']) : Factory::getApplication()->getIdentity()->id;

				$attendingsModel = new AttendingsModel();
				$attendings = $attendingsModel->getAttendingsForUser($userId);

				foreach ($attendings as $attending) {
					$attendingForm = new AttendingModel();
					$attArray = get_object_vars($attending);

					if($attending->fittings) {
						$fittingArray = json_decode($attArray['fittings'],true);
					} else {
						$fittingArray = array();
					}

					$fittingArray[] = $itemId;
					$attArray['fittings'] = $fittingArray;
					$attendingForm->save($attArray);
				}
			}
		}
	}

	/**
	 * Delete fitting after checking all attendings and deleting the fitting there
	 *
	 * @return void
	 * @throws Exception
	 *
	 * @since  1.1.5
	 */
	public function delete():void
	{
		$pks = $this->input->get('cid');

		if ($pks)
		{
			$ffM = new FittingformModel;
			$afM = new AttendingModel;
			$attsModel = new AttendingsModel;

			foreach ($pks as &$pk)
			{
				$item = $ffM->getItem($pk);
				$atts = $attsModel->getAttendingsForUser($item->user_id);

				foreach ($atts as $att)
				{
					if($att->fittings)
					{
						$attArray = get_object_vars($att);
						$fittingArray = json_decode($attArray['fittings'],true);
						$index = array_search($pk, $fittingArray);
						array_splice($fittingArray,$index,1);
						$attArray['fittings']=$fittingArray;

						try
						{
							$afM->save($attArray);
						}
						catch (Exception $e)
						{
						}
					}
				}

				$ffM->delete($pk);
			}
		}

		$this->setRedirect(Route::_($this->getReturnPage(), false));
	}
}
