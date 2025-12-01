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
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemModelInterface;
use Sda\Component\Sdajem\Administrator\Library\Item\FittingTableItem;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @package     Sda\Component\Sdajem\Administrator\Model
 * @since       1.1.1
 */
class FittingModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_sdajem.fitting';

	/**
	 * @param   array  $data     An optional associative array of data for the model.
	 * @param   bool   $loadData True if the model instance should load its own data (if available); false to return a clean empty object.
	 *
	 * @return Form|false
	 * @since 1.0.0
	 *
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		try
		{
			$app  = Factory::getApplication();
			$form = $this->loadForm($this->typeAlias, 'fitting', ['control' => 'jform', 'load_data' => $loadData]);
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');

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
	protected function loadFormData()
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_sdajem.edit.fitting.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->typeAlias, $data);

		return FittingTableItem::createFromObject($data);
	}

	/**
	 * @since 1.5.3
	 *
	 * @param   int|null  $pk  The primary key of the item to retrieve.
	 *                         Retrieves an item as a FittingTableItem instance.
	 *
	 * @return FittingTableItem The item retrieved and returned as a FittingTableItem instance.
	 */
	public function getItem($pk = null): FittingTableItem
	{
		return FittingTableItem::createFromObject(parent::getItem($pk));
	}
}
