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
use Joomla\Utilities\ArrayHelper;
use Sda\Component\Sdajem\Administrator\Library\Interface\ItemModelInterface;
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
}
