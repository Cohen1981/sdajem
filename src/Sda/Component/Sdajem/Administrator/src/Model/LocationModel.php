<?php

/**
 * @copyright (c) 2025 Alexander Bahlo <abahlo@hotmail.de>
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Sda\Component\Sdajem\Administrator\Model;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Sda\Component\Sdajem\Administrator\Library\Item\LocationTableItem;
use Sda\Component\Sdajem\Administrator\Table\LocationTable;

/**
 * @package     Sda\Component\Sdajem\Administrator\Model
 * @since       1.0.0
 */
class LocationModel extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	public $typeAlias = 'com_sdajem.location';

	/**
	 * @since 1.0.0
	 *
	 * @param   bool   $loadData
	 * @param   array  $data
	 *
	 * @return Form|false
	 */
	public function getForm($data = array(), $loadData = true): false|Form
	{
		// Get the form.
		try
		{
			$app  = Factory::getApplication();
			$form = $this->loadForm($this->typeAlias, 'location', ['control' => 'jform', 'load_data' => $loadData]);
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
	protected function loadFormData(): LocationTableItem
	{
		$app = Factory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_sdajem.edit.location.data', []);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$this->preprocessData($this->typeAlias, $data);

		return LocationTableItem::createFromObject($data);
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since   1.0.0
	 *
	 * @param   LocationTable  $table  The Table object
	 *
	 * @return  void
	 */
	protected function prepareTable($table): void
	{
		$table->check();
		$table->generateAlias();
	}

	/**
	 * Retrieves an item from the parent, converts it into a LocationTableItem instance, and returns it.
	 *
	 * @since 1.0.0
	 *
	 * @param   int|null  $pk  The primary key of the item to retrieve.
	 *
	 * @return LocationTableItem The converted LocationTableItem instance.
	 */
	public function getItem($pk = null): LocationTableItem
	{
		return LocationTableItem::createFromObject(parent::getItem($pk));
	}
}
