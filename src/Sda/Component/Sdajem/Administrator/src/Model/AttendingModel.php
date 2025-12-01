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
use Sda\Component\Sdajem\Administrator\Library\Collection\AttendingTableItemsCollection;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemModelInterface;
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
}
